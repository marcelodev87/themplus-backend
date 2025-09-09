<?php

namespace App\Services;

use App\Helpers\RoleHelper;
use App\Repositories\RoleRepository;
use App\Rules\RoleRule;

class RoleService
{
    protected $rule;

    protected $repository;

    public function __construct(
        RoleRule $rule,
        RoleRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        RoleHelper::existsRole(
            $request->user()->enterprise_id,
            $request->input('name'),
            'create',
        );

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        RoleHelper::existsRole(
            $request->user()->enterprise_id,
            $request->input('name'),
            'update',
            $request->input('id')
        );

        $data = $request->only(['name']);

        return $this->repository->update($request->input('id'), $data);
    }
}
