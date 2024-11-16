<?php

namespace App\Services;

use App\Repositories\AlertRepository;
use App\Rules\AlertRule;

class AlertService
{
    protected $rule;

    protected $repository;

    public function __construct(
        AlertRule $rule,
        AlertRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['description']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->create($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = $request->only(['description']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->update($request->input('id'), $data);
    }
}
