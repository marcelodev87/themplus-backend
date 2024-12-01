<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\FinancialRepository;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialController
{
    private $service;

    private $repository;

    public function __construct(FinancialService $service, FinancialRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $deliveries = $this->repository->mountDeliveries($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['deliveries' => $deliveries, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as entregas: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request)
    {
        try {
            DB::beginTransaction();
            $delivery = $this->service->finalize($request);

            if ($delivery) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $deliveries = $this->repository->mountDeliveries($enterpriseId);

                return response()->json(['deliveries' => $deliveries, 'message' => 'Entrega realizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao realizar entrega');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao realizar entrega: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
