<?php

namespace App\Http\Controllers;

use App\Helpers\RegisterHelper;
use App\Repositories\CategoryRepository;
use App\Repositories\EnterpriseRepository;
use App\Rules\AlertRule;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlertController
{
    private $service;

    private $repository;

    private $enterpriseRepository;

    private $rule;

    public function __construct(AlertService $service, CategoryRepository $repository, AlertRule $rule, EnterpriseRepository $enterpriseRepository)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public function index($id)
    {
        try {
            $categories = $this->repository->getAllByEnterpriseWithDefaults($id);

            return response()->json(['categories' => $categories], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as categorias: '.$e->getMessage());

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
            $this->service->update($request);
            $category = $this->repository->findById($request->input('categories')[0]['id']);
            $enterprise = $this->enterpriseRepository->findById($category->enterprise_id);

            RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'alert',
                "{$enterprise->name}|{$enterprise->email}"
            );

            DB::commit();

            $categories = $this->repository->getAllByEnterpriseWithDefaults($request->input('enterpriseId'));

            return response()->json(['categories' => $categories, 'message' => 'Categorias com alertas atualizadas com sucesso'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar categoria com alerta: '.$e->getMessage());

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
