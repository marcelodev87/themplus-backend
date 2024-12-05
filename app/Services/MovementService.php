<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MovementRepository;
use App\Rules\MovementRule;
use Carbon\Carbon;

class MovementService
{
    protected $rule;

    protected $repository;

    protected $accountRepository;

    protected $categoryRepository;

    public function __construct(
        MovementRule $rule,
        MovementRepository $repository,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->accountRepository = $accountRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('receipts');
        }

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'date_movement' => Carbon::createFromFormat('d/m/Y', $request->input('date'))->format('Y-m-d'),
            'description' => $request->input('description'),
            'receipt' => $filePath,
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        $movement = $this->repository->create($data);
        if ($movement) {
            return $this->updateBalanceAccount($request->input('account'));
        }

        return null;
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

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'description' => $request->input('description'),
            'receipt' => $request->input('file'),
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        $movement = $this->repository->update($request->input('id'), $data);

        if ($movement) {
            return $this->updateBalanceAccount($request->input('account'));
        }

        return null;
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
