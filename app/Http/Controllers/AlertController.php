<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\AlertRepository;
use App\Rules\AlertRule;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlertController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(AlertService $service, AlertRepository $repository, AlertRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $alerts = $this->repository->getAllByEnterprise($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['alerts' => $alerts, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as alertas: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $alert = $this->service->create($request);

            if ($alert) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $alerts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['alerts' => $alerts, 'message' => 'Alerta cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar alerta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar alerta: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $alert = $this->service->update($request);

            if ($alert) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $alerts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['alerts' => $alerts, 'message' => 'Alerta atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar alerta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar alerta: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $alert = $this->repository->delete($id);

            if ($alert) {
                DB::commit();

                return response()->json(['message' => 'Alerta excluída com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar alerta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar alerta: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
