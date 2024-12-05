<?php

namespace App\Http\Controllers;

use App\Exports\SchedulingExport;
use App\Helpers\EnterpriseHelper;
use App\Http\Resources\AccountResource;
use App\Http\Resources\CategoryResource;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SchedulingRepository;
use App\Rules\SchedulingRule;
use App\Services\SchedulingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchedulingController
{
    private $service;

    private $repository;

    private $rule;

    private $categoryRepository;

    private $accountRepository;

    public function __construct(
        SchedulingService $service,
        SchedulingRepository $repository,
        SchedulingRule $rule,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
        $this->categoryRepository = $categoryRepository;
        $this->accountRepository = $accountRepository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $schedulings = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['schedulings' => $schedulings, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os agendamentos: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filterSchedulings(Request $request)
    {
        try {
            $schedulings = $this->repository->getAllByEnterpriseWithRelationsWithParams($request);

            return response()->json(['schedulings' => $schedulings], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar agendamentos com base nos filtros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $out = filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN);
        $entry = filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN);
        $expired = filter_var($request->query('expired'), FILTER_VALIDATE_BOOLEAN);
        $enterpriseId = $request->user()->enterprise_id;

        $schedulings = $this->repository->export($out, $entry, $expired, $enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "schedulings_{$enterpriseId}_{$dateTime}.xlsx";

        return (new SchedulingExport($schedulings))->download($fileName);
    }

    public function getFormInformations(Request $request, $type)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;

            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaultsOnlyActive($enterpriseId, $type);
            $accounts = $this->accountRepository->getAllByEnterpriseOnlyActive($enterpriseId);

            return response()->json(['categories' => CategoryResource::collection($categories),
                'accounts' => AccountResource::collection($accounts), ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $scheduling = $this->service->create($request);

            if ($scheduling) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $schedulings = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento cadastrado com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $scheduling = $this->service->update($request);

            if ($scheduling) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $schedulings = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento atualizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->rule->finalize($id);
            $movement = $this->repository->finalize($id);

            if ($movement) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $schedulings = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento finalizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao finalizar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao finalizar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $this->rule->delete($id);
            $scheduling = $this->repository->delete($id);

            if ($scheduling) {
                DB::commit();

                return response()->json(['message' => 'Agendamento deletado com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
