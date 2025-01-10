<?php

namespace App\Http\Controllers;

use App\Helpers\RegisterHelper;
use App\Repositories\FinancialRepository;
use App\Repositories\MovementRepository;
use App\Rules\ReportRule;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController
{
    private $service;

    protected $rule;

    private $financialRepository;

    private $movementRepository;

    public function __construct(ReportService $service, FinancialRepository $financialRepository, ReportRule $rule, MovementRepository $movementRepository)
    {
        $this->service = $service;
        $this->rule = $rule;
        $this->financialRepository = $financialRepository;
        $this->movementRepository = $movementRepository;
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

    public function details($id)
    {
        try {
            $this->rule->details($id);

            $financial = $this->financialRepository->findById($id);
            $month = str_pad($financial->month, 2, '0', STR_PAD_LEFT);
            $data = "{$month}-{$financial->year}";
            $movements = $this->movementRepository->getAllByEnterpriseWithRelationsByDate($financial->enterprise_id, $data);

            return response()->json([
                'movements' => $movements,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar detalhes do relatório do cliente: '.$e->getMessage());

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
                    'message' => 'Relatório finalizado com sucesso',
                ], 200);
            }
            throw new \Exception('Falha ao finalizar relatório');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao finalizar relatório do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reopen(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->reopen($id);
            $report = $this->financialRepository->findById($id);
            $this->financialRepository->delete($id);

            $enterpriseName = $report->enterprise->name;

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'report',
                "$report->month/$report->year|$enterpriseName"
            );

            if ($report && $register) {
                DB::commit();

                $result = $this->service->index($request, $report->enterprise_id);

                return response()->json([
                    'reports' => $result['reports'],
                    'client_name' => $result['client_name'],
                    'message' => 'Relatório reaberto com sucesso',
                ], 200);
            }
            throw new \Exception('Falha ao reverter relatório para não verificado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao reverter relatório para não verificado do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function undo(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $financial = $this->financialRepository->findById($id);
            $report = $this->financialRepository->update($id, ['check_counter' => null]);
            $enterpriseName = $financial->enterprise->name;

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'undone',
                'report',
                "$report->month/$report->year|$enterpriseName"
            );

            if ($report && $register) {
                DB::commit();

                $result = $this->service->index($request, $report->enterprise_id);

                return response()->json([
                    'reports' => $result['reports'],
                    'client_name' => $result['client_name'],
                    'message' => 'Verificação de relatório removida com sucesso',
                ], 200);
            }
            throw new \Exception('Falha ao reverter relatório para não verificado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao reverter relatório para não verificado do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
