<?php

namespace App\Services;

use App\Helpers\RegisterHelper;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\MovementRepository;
use App\Rules\MovementRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MovementService
{
    protected $rule;

    protected $repository;

    protected $accountRepository;

    protected $categoryRepository;

    protected $financialRepository;

    public function __construct(
        MovementRule $rule,
        MovementRepository $repository,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
        FinancialRepository $financialRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->accountRepository = $accountRepository;
        $this->categoryRepository = $categoryRepository;
        $this->financialRepository = $financialRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $fileUrl = null;

        if ($request->hasFile('file')) {
            $env = env('APP_ENV');

            $folder = match ($env) {
                'local' => 'receipts-local',
                'development' => 'receipts-development',
                'production' => 'receipts-production',
                default => 'receipts',
            };

            $path = $request->file('file')->store($folder, 's3');

            $fileUrl = Storage::disk('s3')->url($path);
        }

        $programed = $request->input('programmed');
        $initialDate = Carbon::createFromFormat('d/m/Y', $request->input('date'));

        $createdMovements = [];

        $financial = $this->financialRepository->getReports($request->user()->enterprise_id);
        for ($i = 0; $i <= $programed; $i++) {

            $month = $initialDate->month;
            $year = $initialDate->year;

            $exists = $financial->where('month', $month)->where('year', $year)->isNotEmpty();
            if ($exists) {
                throw new \Exception('Nos períodos de movimentações mencionados, contém períodos em que os relatórios ja foram entregues');
            }

            $data = [
                'type' => $request->input('type'),
                'value' => $request->input('value'),
                'date_movement' => $initialDate->format('Y-m-d'),
                'description' => $request->input('description'),
                'receipt' => $fileUrl,
                'category_id' => $request->input('category'),
                'account_id' => $request->input('account'),
                'enterprise_id' => $request->user()->enterprise_id,
            ];

            $movement = $this->repository->create($data);
            if ($movement) {
                $createdMovements[] = $movement;
                $this->updateBalanceAccount($request->input('account'));
            }

            $initialDate->addMonth();
            $initialDate->endOfMonth();
        }

        return $createdMovements;
    }

    public function insert($request)
    {
        $this->rule->insert($request);
        $createdMovements = [];

        $movementsArray = $request->input('movements');

        foreach ($movementsArray as $index => $movementData) {
            $fileUrl = null;

            if ($request->hasFile("movements.$index.receipt")) {
                $file = $request->file("movements.$index.receipt");

                $env = env('APP_ENV');
                $folder = match ($env) {
                    'local' => 'receipts-local',
                    'development' => 'receipts-development',
                    'production' => 'receipts-production',
                    default => 'receipts',
                };

                $path = $file->store($folder, 's3');
                $fileUrl = Storage::disk('s3')->url($path);
            }

            $data = [
                'type' => $movementData['type'],
                'date_movement' => Carbon::createFromFormat('d/m/Y', $movementData['date']),
                'value' => $movementData['value'],
                'description' => $movementData['description'],
                'receipt' => $fileUrl,
                'category_id' => $movementData['category'],
                'account_id' => $movementData['account'],
                'enterprise_id' => $request->user()->enterprise_id,
            ];

            $movement = $this->repository->create($data);
            if ($movement) {
                $createdMovements[] = $movement;
                $this->updateBalanceAccount($movementData['account']);

                RegisterHelper::create(
                    $request->user()->id,
                    $request->user()->enterprise_id,
                    'insert',
                    'movement',
                    "{$movement->value}|{$movement->type}|{$movement->account->name}|{$movement->account->account_number}|{$movement->account->agency_number}|{$movement->category->name}|{$movement->date_movement}"
                );
            }
        }

        return response()->json($createdMovements);
    }

    public function createTransfer($dataOut, $dataEntry)
    {

        $out = $this->repository->create($dataOut);
        $entry = $this->repository->create($dataEntry);
        if ($out && $entry) {
            $movementsOut = $this->repository->getAllByAccount($dataOut['account_id']);
            $movementsEntry = $this->repository->getAllByAccount($dataEntry['account_id']);

            $newValueAccountOut = $this->calculateValueAccount($movementsOut);
            $newValueAccountEntry = $this->calculateValueAccount($movementsEntry);

            $out = $this->accountRepository->updateBalance(
                $dataOut['account_id'],
                $newValueAccountOut
            );
            $entry = $this->accountRepository->updateBalance(
                $dataEntry['account_id'],
                $newValueAccountEntry
            );

            return ['out' => $out, 'entry' => $entry];
        }

        return null;
    }

    public function update($request)
    {
        $this->rule->update($request);
        $movement = $this->repository->findById($request->input('id'));

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        $data = $this->handleFileUpdate($request, $movement, $data);

        $movement = $this->repository->update($request->input('id'), $data);

        if ($movement) {
            return $this->updateBalanceAccount($request->input('account'));
        }

        return null;
    }

    public function updateMovementByCounter($request)
    {
        $this->rule->update($request);
        $movement = $this->repository->findById($request->input('id'));

        $data = [
            'value' => $request->input('value'),
            'description' => $request->input('description'),
        ];

        $movement = $this->repository->update($request->input('id'), $data);

        if ($movement) {
            return $this->updateBalanceAccount($request->input('account'));
        }

        return null;
    }

    private function handleFileUpdate($request, $movement, $data)
    {
        if ($request->input('file') === 'keep') {
            return $data;
        } elseif (! $request->hasFile('file')) {
            $data['receipt'] = null;
            if ($movement && $movement->receipt) {
                $oldFilePath = str_replace(env('AWS_URL').'/', '', $movement->receipt);
                if ($oldFilePath) {
                    Storage::disk('s3')->delete($oldFilePath);
                }
            }

            return $data;
        } else {
            if ($movement && $movement->receipt) {
                $oldFilePath = str_replace(env('AWS_URL').'/', '', $movement->receipt);
                if ($oldFilePath) {
                    Storage::disk('s3')->delete($oldFilePath);
                }
            }
            $env = env('APP_ENV');
            $folder = match ($env) {
                'local' => 'receipts-local',
                'development' => 'receipts-development',
                'production' => 'receipts-production',
                default => 'receipts',
            };

            $path = $request->file('file')->store($folder, 's3');
            $data['receipt'] = Storage::disk('s3')->url($path);

            return $data;
        }
    }

    public function saveObservations($request)
    {
        foreach ($request->movements as $movement) {
            $observationValue = ($movement['observation'] === null || trim($movement['observation']) === '')
                ? null
                : $movement['observation'];

            $this->repository->update($movement['id'], ['observation' => $observationValue]);
        }
    }

    public function includeScheduling($scheduling)
    {

        $data = [
            'type' => $scheduling['type'],
            'value' => $scheduling['value'],
            'date_movement' => Carbon::now('America/Sao_Paulo')->format('Y-m-d'),
            'description' => $scheduling['description'],
            'receipt' => $scheduling['receipt'],
            'category_id' => $scheduling['category_id'],
            'account_id' => $scheduling['account_id'],
            'enterprise_id' => $scheduling['enterprise_id'],
        ];

        $movement = $this->repository->create($data);

        if ($movement) {
            return $this->updateBalanceAccount($scheduling['account_id']);
        }

        return null;
    }

    public function calculateValueAccount($movements)
    {
        $value = 0;

        foreach ($movements as $movement) {
            if ($movement->type === 'entrada') {
                $value += $movement->value;
            } elseif ($movement->type === 'saída') {
                $value -= $movement->value;
            } else {
                $category = $this->categoryRepository->findById($movement->category_id);
                if ($category->type === 'entrada') {
                    $value += $movement->value;
                } else {
                    $value -= $movement->value;
                }
            }
        }

        return $value;
    }

    public function updateBalanceAccount($accountId)
    {
        $movements = $this->repository->getAllByAccount($accountId);
        $newValueAccount = $this->calculateValueAccount($movements);

        return $this->accountRepository->updateBalance($accountId, $newValueAccount);
    }
}
