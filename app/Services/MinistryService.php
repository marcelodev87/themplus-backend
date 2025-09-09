<?php

namespace App\Services;

use App\Repositories\MinistryRepository;
use App\Rules\MinistryRule;

class MinistryService
{
    protected $rule;

    protected $repository;

    public function __construct(
        MinistryRule $rule,
        MinistryRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name', 'member_id']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = $request->only(['name', 'member_id']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->update($request->input('id'), $data);
    }
}
