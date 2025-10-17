<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\MinistryRepository;
use App\Rules\MinistryRule;
use App\Services\MinistryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MinistryController
{
    private $service;

    private $repository;

    private $enterpriseRepository;

    private $rule;

    public function __construct(MinistryService $service, MinistryRepository $repository, MinistryRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $ministries = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['member']);
            $filledData = EnterpriseHelper::filledData($request->user()->enterprise_id);

            return response()->json(['filled_data' => $filledData, 'ministries' => $ministries], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os ministérios: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            EnterpriseHelper::allowHeadquarters($request->user()->enterprise_id);

            $ministry = $this->service->create($request);

            if ($ministry) {
                DB::commit();

                $ministries = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['member']);

                return response()->json(['ministries' => $ministries, 'message' => 'Ministério cadastrado com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar ministério');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar ministério: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            EnterpriseHelper::allowHeadquarters($request->user()->enterprise_id);
            $ministry = $this->service->update($request);

            if ($ministry) {
                DB::commit();

                $ministries = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['member']);

                return response()->json(['ministries' => $ministries, 'message' => 'Ministério atualizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar ministério');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar ministério: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            EnterpriseHelper::allowHeadquarters($request->user()->enterprise_id);

            $this->rule->delete($id);
            $ministry = $this->repository->delete($id);

            if ($ministry) {
                DB::commit();
                $ministries = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['member']);

                return response()->json(['ministries' => $ministries, 'message' => 'Ministério excluído com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar ministério');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar ministério: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
