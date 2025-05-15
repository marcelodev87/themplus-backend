<?php

namespace App\Services;

use App\Repositories\EnterpriseRepository;
use App\Repositories\FinancialMovementReceiptRepository;
use App\Repositories\FinancialRepository;
use App\Rules\ReportRule;

class ReportService
{
    protected $rule;

    protected $financialRepository;

    protected $financialMovementReceiptRepository;

    protected $enterpriseRepository;

    public function __construct(
        ReportRule $rule,
        FinancialRepository $financialRepository,
        EnterpriseRepository $enterpriseRepository,
        FinancialMovementReceiptRepository $financialMovementReceiptRepository
    ) {
        $this->rule = $rule;
        $this->financialRepository = $financialRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->financialMovementReceiptRepository = $financialMovementReceiptRepository;
    }

    public function index($request, $id)
    {
        $this->rule->index($id);
        $bonds = $this->enterpriseRepository->getBonds($request->user()->enterprise_id);

        if ($bonds->contains('id', $id)) {
            $reports = $this->financialRepository->getReports($id);
            $client = $this->enterpriseRepository->findById($id);

            $formattedReports = $reports->map(function ($report) {

                $receipts = $this->financialMovementReceiptRepository->countAllByFinancial($report->id);

                return [
                    'id' => $report->id,
                    'date_delivery' => $report->date_delivery,
                    'month_year' => "$report->month/$report->year",
                    'check_counter' => $report->check_counter,
                    'receipts' => $receipts,
                ];
            });

            return ['reports' => $formattedReports, 'client_name' => $client->name];
        } else {
            throw new \Exception('Sua organização não tem acesso aos dados desse cliente');
        }
    }

    public function finalize($request, $id)
    {
        $this->rule->index($id);

        return $this->financialRepository->update($id, ['check_counter' => $request->user()->enterprise_id]);
    }
}
