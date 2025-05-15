<?php

namespace App\Http\Controllers;

use App\Repositories\FinancialMovementReceiptRepository;
use App\Repositories\FinancialRepository;
use App\Rules\FinancialMovementReceiptRule;
use App\Services\FinancialMovementReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialReceiptController
{
    private $repository;

    private $service;

    private $rule;

    private $financialRepository;

    public function __construct(FinancialMovementReceiptRepository $repository, FinancialMovementReceiptRule $rule, FinancialRepository $financialRepository, FinancialMovementReceiptService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->rule = $rule;
        $this->financialRepository = $financialRepository;
    }

    public function index(Request $request, $monthYear)
    {
        try {
            $financial = $this->financialRepository->getReportByDateAndEnterprise($request->user()->enterprise_id, $monthYear);
            $receipts = $this->repository->getAllByFinancial($financial->id);

            return response()->json(['files_financial' => $receipts], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todos os arquivos: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, $monthYear)
    {
        try {
            DB::beginTransaction();

            $financial = $this->financialRepository->getReportByDateAndEnterprise($request->user()->enterprise_id, $monthYear);
            $receipt = $this->service->create($request, $financial->id);

            if ($receipt) {
                DB::commit();
                $receipts = $this->repository->getAllByFinancial($financial->id);

                return response()->json([
                    'files_financial' => $receipts,
                    'message' => 'Anexado arquivo á entrega com sucesso',
                ], 201);
            }

            throw new \Exception('Falha ao anexar arquivo á entrega');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao anexar arquivo á entrega: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $receipt = $this->repository->delete($id);

            if ($receipt) {
                DB::commit();

                return response()->json(['message' => 'Anexo excluído com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar anexo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar anexo: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
