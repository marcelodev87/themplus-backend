<?php

namespace App\Services;

use App\Repositories\SchedulingRepository;
use App\Rules\SchedulingRule;
use App\Repositories\FinancialRepository;
use Carbon\Carbon;

class SchedulingService
{
    protected $rule;

    protected $repository;

    protected $financialRepository;

    public function __construct(
        SchedulingRule $rule,
        SchedulingRepository $repository,
        FinancialRepository $financialRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->financialRepository = $financialRepository;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('receipts');
        }

        $programmed = (int) $request->input('programmed');
        $initialDate = Carbon::createFromFormat('d/m/Y', $request->input('date'));

        $createdSchedulings = [];

        $financial = $this->financialRepository->getReports($request->user()->enterprise_id);

        for ($i = 0; $i <= $programmed; $i++) {

            $month = $initialDate->month;
            $year = $initialDate->year;

            $exists = $financial->where('month', $month)->where('year', $year)->isNotEmpty();
            if ($exists) {
                throw new \Exception('Nos períodos de agendamentos mencionados, contém períodos em que os relatórios ja foram entregues');
            }
            $data = [
                'type' => $request->input('type'),
                'value' => $request->input('value'),
                'date_movement' => $initialDate->format('Y-m-d'),
                'description' => $request->input('description'),
                'receipt' => $filePath,
                'category_id' => $request->input('category'),
                'account_id' => $request->input('account'),
                'enterprise_id' => $request->user()->enterprise_id,
            ];

            $scheduling = $this->repository->create($data);
            if ($scheduling) {
                $createdSchedulings[] = $scheduling;
            }

            $initialDate->addMonthNoOverflow();
        }

        return $createdSchedulings;
    }

    public function update($request)
    {
        $this->rule->update($request);

        $data = [
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'description' => $request->input('description'),
            'date_movement' => Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d'),
            'receipt' => $request->input('file'),
            'category_id' => $request->input('category'),
            'account_id' => $request->input('account'),
            'enterprise_id' => $request->user()->enterprise_id,
        ];

        return $this->repository->update($request->input('id'), $data);
    }
}
