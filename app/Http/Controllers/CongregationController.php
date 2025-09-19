<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\CongregationRepository;
use App\Rules\CongregationRule;
use App\Services\CongregationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CongregationController
{
    private $service;

    private $repository;

    private $enterpriseRepository;

    private $rule;

    public function __construct(CongregationService $service, CongregationRepository $repository, CongregationRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $congregations = $this->repository->getAllByEnterprise($request->user()->view_enterprise_id, ['member']);
            $filledData = EnterpriseHelper::filledData($request->user()->enterprise_id);

            return response()->json(['filled_data' => $filledData, 'congregations' => $congregations], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as congregações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $congregation = $this->service->create($request);

            if ($congregation) {
                DB::commit();

                $congregations = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['member']);

                return response()->json(['congregations' => $congregations, 'message' => 'Congregação cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar congregação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar congregação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $congregation = $this->service->update($request);

            if ($congregation) {
                DB::commit();

                $congregations = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['member']);

                return response()->json(['congregations' => $congregations, 'message' => 'Congregação atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar congregação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar congregação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $congregation = $this->repository->delete($id);

            if ($congregation) {
                DB::commit();

                $congregations = $this->repository->getAllByEnterprise($request->user()->view_enterprise_id, ['member']);

                return response()->json(['congregations' => $congregations, 'message' => 'Congregação excluída com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar congregação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar congregação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
