<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\MovementRepository;
use App\Rules\MovementRule;
use Carbon\Carbon;

class MovementService
{
    protected $rule;

    protected $repository;

    protected $accountRepository;

    public function __construct(
        MovementRule $rule,
        MovementRepository $repository,
        AccountRepository $accountRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->accountRepository = $accountRepository;
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
            $movements = $this->repository->getAllByEnterprise($request->user()->enterprise_id);
            $newValueAccount = $this->calculateValueAccount($movements);

            return $this->accountRepository->updateBalance(
                $request->input('account'),
                $newValueAccount
            );
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
            $movements = $this->repository->getAllByAccount(
                $request->user()->enterprise_id,
                $request->input('account')
            );
            $newValueAccount = $this->calculateValueAccount($movements);

            return $this->accountRepository->updateBalance(
                $request->input('account'),
                $newValueAccount
            );
        }

        return null;
    }

    public function includeScheduling($scheduling)
    {

        $data = [
            'type' => $scheduling['type'],
            'value' => $scheduling['value'],
            'date_movement' => $scheduling['date_movement'],
            'description' => $scheduling['description'],
            'receipt' => $scheduling['receipt'],
            'category_id' => $scheduling['category_id'],
            'account_id' => $scheduling['account_id'],
            'enterprise_id' => $scheduling['enterprise_id'],
        ];

        $movement = $this->repository->create($data);

        if ($movement) {
            $movements = $this->repository->getAllByEnterprise($scheduling['enterprise_id']);
            $newValueAccount = $this->calculateValueAccount($movements);

            return $this->accountRepository->updateBalance(
                $scheduling['account_id'],
                $newValueAccount
            );
        }

        return null;
    }

    public function calculateValueAccount($movements)
    {
        $value = 0;

        foreach ($movements as $movement) {
            $value += ($movement->type === 'entrada' ? 1 : -1) * $movement->value;
        }

        return $value;
    }
}
