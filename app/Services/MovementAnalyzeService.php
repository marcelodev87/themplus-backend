<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\MovementAnalyzeRepository;
use App\Repositories\MovementRepository;
use App\Repositories\UserRepository;
use App\Rules\MovementRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MovementAnalyzeService
{
    protected $rule;
    protected $repository;

    protected $movementRepository;

    protected $accountRepository;
    protected $userRepository;

    protected $categoryRepository;

    protected $financialRepository;

    public function __construct(
        MovementRule $rule,
        MovementRepository $movementRepository,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
        FinancialRepository $financialRepository,
        MovementAnalyzeRepository $repository,
        UserRepository $userRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->movementRepository = $movementRepository;
        $this->accountRepository = $accountRepository;
        $this->categoryRepository = $categoryRepository;
        $this->financialRepository = $financialRepository;
        $this->userRepository = $userRepository;
    }

    public function create($request)
    {
        $enterpriseId = $this->verifyUserByPhone($request->input('phone'));
        $fileUrl = $this->saveFile($request);

        $data = [
            'date_movement' => Carbon::parse($request->input('date'))->format('Y-m-d'),
            'value' => $request->input('value'),
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'file' => $fileUrl,
            'enterprise_id' => $enterpriseId
        ];
        return $this->repository->create($data);
    }
    public function finalize($request)
    {
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

        $initialDate = Carbon::createFromFormat('Y-m-d', $request->input('date'));

        $financial = $this->financialRepository->getReports($request->user()->enterprise_id);

        $month = $initialDate->month;
        $year = $initialDate->year;

        $exists = $financial->where('month', $month)->where('year', $year)->isNotEmpty();
        if ($exists) {
            throw new \Exception('No período de movimentação mencionado, contém períodos em que os relatórios ja foram entregues');
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

        $movement = $this->movementRepository->create($data);
        if ($movement) {
            $this->updateBalanceAccount($request->input('account'));
            $this->repository->delete($request->input('id'));
        }

        return $movement;
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
        $movements = $this->movementRepository->getAllByAccount($accountId);
        $newValueAccount = $this->calculateValueAccount($movements);

        return $this->accountRepository->updateBalance($accountId, $newValueAccount);
    }

    public function verifyUserByPhone($phone)
    {
        $users = $this->userRepository->getUsersByPhone($phone);

        if (count($users) === 0) {
            throw new \Exception(
                'Nenhum usuário no sistema está registrado com esse número de telefone',
                404
            );
        }

        if (count($users) > 1) {
            throw new \Exception(
                'O mesmo número de telefone está registrado em mais de um usuário, entre em contato com o administrador',
                403
            );
        }

        $user = $users[0]->toArray();
        return $user['enterprise_id'];
    }

    public function saveFile($request)
    {
        $fileUrl = null;
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
                throw new \Exception('Só é permitido arquivos do formato PDF');
            }

            if ($file->getSize() > 2097152) {
                throw new \Exception('O limite de arquivo PDF é até 2mb');
            }

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

        return $fileUrl;
    }
}
