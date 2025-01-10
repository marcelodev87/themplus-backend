<?php

namespace App\Http\Controllers;

use App\Helpers\RegisterHelper;
use App\Repositories\FinancialRepository;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController
{
    private $service;

    private $financialRepository;

    public function __construct(ReportService $service, FinancialRepository $financialRepository)
    {
        $this->service = $service;
        $this->financialRepository = $financialRepository;
    }

    public function index(Request $request, $id)
    {
        try {
            $data = $this->service->index($request, $id);

            return response()->json([
                'reports' => $data['reports'],
                'client_name' => $data['client_name'],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar relatório do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $report = $this->financialRepository->findById($id);
            $data = $this->service->finalize($request, $id);
            $enterpriseName = $report->enterprise->name;

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'finalized',
                'report',
                "$report->month/$report->year|$enterpriseName"
            );

            if ($data && $register) {
                DB::commit();

                $result = $this->service->index($request, $report->enterprise_id);

                return response()->json([
                    'reports' => $result['reports'],
                    'client_name' => $result['client_name'],
                ], 200);
            }
            throw new \Exception('Falha ao finalizar relatório');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao finalizar relatório do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
