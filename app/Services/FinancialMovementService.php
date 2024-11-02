<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\FinancialMovementRepository;
use App\Rules\FinancialMovementRule;

class FinancialMovementService
{
    protected $rule;

    protected $repository;

    protected $accountRepository;

    public function __construct(
        FinancialMovementRule $rule,
        FinancialMovementRepository $repository,
        AccountRepository $accountRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->accountRepository = $accountRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'date_movement' => $request->input('date'),
            'description' => $request->input('description'),
            'receipt' => $request->input('file'),
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        $movement = $this->repository->create($data);
        if ($movement) {
            $movement = $this->accountRepository->updateBalance(
                $request->input('account'),
                $request->input('type'),
                $request->input('value')
            );
        }

        return $movement;
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

        return $this->repository->update($request->input('id'), $data);
    }
}
