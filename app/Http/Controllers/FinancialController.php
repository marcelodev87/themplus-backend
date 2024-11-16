<?php

namespace App\Http\Controllers;

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

            return response()->json(['deliveries' => $deliveries], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as entregas: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }

    // public function finalize(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $alert = $this->service->update($request);

    //         if ($alert) {
    //             DB::commit();

    //             $enterpriseId = $request->user()->enterprise_id;
    //             $alerts = $this->repository->getAllByEnterprise($enterpriseId);

    //             return response()->json(['alerts' => $alerts, 'message' => 'Alerta atualizada com sucesso'], 200);
    //         }

    //         throw new \Exception('Falha ao atualizar alerta');
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Log::error('Erro ao atualizar alerta: '.$e->getMessage());

    //         return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
    //     }
    // }

}
