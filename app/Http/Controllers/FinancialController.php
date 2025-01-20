<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\RegisterHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\OrderRepository;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialController
{
    private $service;

    private $repository;

    private $enterpriseRepository;

    private $orderRepository;

    public function __construct(FinancialService $service, FinancialRepository $repository, EnterpriseRepository $enterpriseRepository, OrderRepository $orderRepository)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
            $deliveries = $this->repository->mountDeliveries($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $enterprise = $this->enterpriseRepository->findById($request->user()->view_enterprise_id);
            $orderCount = $this->orderRepository->getAllByUser($request->user()->enterprise_id)->count();

            return response()->json([
                'deliveries' => $deliveries,
                'counter' => $enterprise->counter_enterprise_id,
                'filled_data' => $filledData,
                'order_count' => $orderCount,
                'is_headquarters' => $enterprise->created_by === null ? true : false,
            ], 200);
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

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'delivered',
                'report',
                "{$request->input('monthYear')}"
            );

            if ($delivery && $register) {
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
