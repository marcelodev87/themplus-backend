<?php

namespace App\Http\Controllers;

use App\Exports\MovementExport;
use App\Http\Resources\AccountResource;
use App\Http\Resources\CategoryResource;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MovementRepository;
use App\Services\MovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovementController
{
    private $service;

    private $repository;

    private $categoryRepository;

    private $accountRepository;

    public function __construct(
        MovementService $service,
        MovementRepository $repository,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->accountRepository = $accountRepository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $movements = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

            return response()->json(['movements' => $movements], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as movimentações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filterMovements(Request $request)
    {
        try {
            $movements = $this->repository->getAllByEnterpriseWithRelationsWithParams($request);

            return response()->json(['movements' => $movements], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar movimentações com base nos filtros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $out = filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN);
        $entry = filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN);
        $enterpriseId = $request->user()->enterprise_id;

        $movements = $this->repository->export($out, $entry, $enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "movements_{$enterpriseId}_{$dateTime}.xlsx";

        return (new MovementExport($movements))->download($fileName);
    }

    public function getFormInformations(Request $request, $type)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;

            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaults($enterpriseId, $type);
            $accounts = $this->accountRepository->getAllByEnterprise($enterpriseId);

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
            $movement = $this->service->create($request);

            if ($movement) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $movements = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['movements' => $movements, 'message' => 'Movimentação cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $movement = $this->service->update($request);

            if ($movement) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $movements = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['movements' => $movements, 'message' => 'Movimentação atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $movement = $this->repository->delete($id);

            if ($movement) {
                DB::commit();

                return response()->json(['message' => 'Movimentação deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
