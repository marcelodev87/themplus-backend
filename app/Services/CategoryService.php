<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Rules\CategoryRule;

class CategoryService
{
    protected $rule;

    protected $repository;

    public function __construct(
        CategoryRule $rule,
        CategoryRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name', 'type']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = $request->only(['name', 'type', 'enterpriseId']);

        return $this->repository->update($request->input('id'), $data);
    }
}
