<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\MovementAnalyzeRepository;
use App\Repositories\MovementRepository;
use App\Rules\MovementRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MovementAnalyzeService
{
    protected $rule;
    protected $repository;

    protected $movementRepository;

    protected $accountRepository;

    protected $categoryRepository;

    protected $financialRepository;

    public function __construct(
        MovementRule $rule,
        MovementRepository $movementRepository,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
        FinancialRepository $financialRepository,
        MovementAnalyzeRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->movementRepository = $movementRepository;
        $this->accountRepository = $accountRepository;
        $this->categoryRepository = $categoryRepository;
        $this->financialRepository = $financialRepository;
    }

    public function create($request)
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

        $initialDate = Carbon::createFromFormat('d/m/Y', $request->input('date'));

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

        $movement = $this->repository->create($data);
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
}
