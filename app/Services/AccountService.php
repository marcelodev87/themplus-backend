<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Rules\AccountRule;

class AccountService
{
    protected $rule;

    protected $repository;

    public function __construct(
        AccountRule $rule,
        AccountRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = [
            'name' => $request->input('name'),
            'balance' => 0,
            'enterprise_id' => $request->user()->enterprise_id,
            'agency_number' => $request->input('agencyNumber'),
            'account_number' => $request->input('accountNumber'),
            'description' => $request->input('description'),
        ];

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = [
            'name' => $request->input('name'),
            'enterprise_id' => $request->user()->enterprise_id,
            'agency_number' => $request->input('agencyNumber'),
            'account_number' => $request->input('accountNumber'),
            'description' => $request->input('description'),
        ];

        return $this->repository->update($request->input('id'), $data);
    }
}
