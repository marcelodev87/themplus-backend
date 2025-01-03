<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\OrderRepository;
use App\Rules\OrderRule;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController
{
    private $repository;

    private $rule;

    private $service;

    public function __construct(OrderRepository $repository, OrderRule $rule, OrderService $service)
    {
        $this->repository = $repository;
        $this->rule = $rule;
        $this->service = $service;
    }

    public function indexViewCounter(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $orders = $this->repository->getAllByCounter($request->user()->enterprise_id);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['orders' => $orders, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as solicitações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function indexViewClient(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $orders = $this->repository->getAllByUser($request->user()->enterprise_id);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['orders' => $orders, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as solicitações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $order = $this->service->create($request);
            if ($order) {
                DB::commit();

                $orders = $this->repository->getAllByCounter($request->user()->enterprise_id);

                return response()->json(['orders' => $orders, 'message' => 'Solicitação enviada com sucesso'], 201);
            }

            throw new \Exception('Falha ao enviar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao enviar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $order = $this->service->update($request);

            if ($order) {
                DB::commit();

                $orders = $this->repository->getAllByCounter($request->user()->enterprise_id);

                return response()->json(['orders' => $orders, 'message' => 'Solicitação atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function actionClient(Request $request)
    {
        try {
            DB::beginTransaction();

            $orderData = $this->repository->findById($request->input('id'));
            $order = $this->service->update($request);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'category',
                $categoryData->name.'|'.$categoryData->type
            );

            if ($category && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $categories = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);

                return response()->json(['categories' => $categories, 'message' => 'Categoria atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar categoria');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar categoria: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $order = $this->repository->delete($id);

            if ($order) {
                DB::commit();

                return response()->json(['message' => 'Solicitação deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
