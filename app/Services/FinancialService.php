<?php

namespace App\Services;

use App\Repositories\FinancialRepository;

class FinancialService
{
    protected $repository;

    public function __construct(
        FinancialRepository $repository,
    ) {
        $this->repository = $repository;
    }

    public function update($request)
    {
        $data = $request->only(['description']);
        $data['enterprise_id'] = $request->user()->enterprise_id;

        return $this->repository->update($request->input('id'), $data);
    }
}
