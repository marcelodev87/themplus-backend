<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use App\Rules\DepartmentRule;

class DepartmentService
{
    protected $rule;

    protected $repository;

    public function __construct(
        DepartmentRule $rule,
        DepartmentRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name']);
        $data['parent_id'] = $request->input('parentId') ?? null;
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = $request->only(['name']);
        $data['parent_id'] = $request->input('parentId') ?? null;

        return $this->repository->update($request->input('id'), $data);
    }
}