<?php

namespace App\Services;

use App\Repositories\FinancialRepository;
use App\Rules\FinancialRule;

class FinancialService
{
    protected $repository;

    protected $rule;

    public function __construct(
        FinancialRepository $repository,
        FinancialRule $rule
    ) {
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function finalize($request)
    {
        $this->rule->finalize($request);

        $currentDate = date('Y-m-d');
        [$month, $year] = explode('/', $request->input('monthYear'));

        $data = [
            'date_delivery' => $currentDate,
            'month' => (int) $month,
            'year' => (int) $year,
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        return $this->repository->create($data);
    }
}
