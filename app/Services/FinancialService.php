<?php

namespace App\Services;

use App\Repositories\FinancialRepository;
use App\Rules\FinancialRule;
use Carbon\Carbon;

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

        $currentDateTime = Carbon::now('America/Sao_Paulo')->format('Y-m-d H:i:s');
        [$month, $year] = explode('/', $request->input('monthYear'));

        $data = [
            'date_delivery' => $currentDateTime,
            'month' => (int) $month,
            'year' => (int) $year,
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        return $this->repository->create($data);
    }
}
