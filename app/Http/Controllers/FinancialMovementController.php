<?php

namespace App\Http\Controllers;

use App\Repositories\FinancialMovementRepository;
use App\Services\FinancialMovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialMovementController
{
    private $service;

    private $repository;

    public function __construct(FinancialMovementService $service, FinancialMovementRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $movements = $this->repository->getAllByEnterprise($enterpriseId);

            return response()->json(['movements' => $movements], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as movimentações: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
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
                $movements = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['movements' => $movements, 'message' => 'Movimentação cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar movimentação: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
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
                $movements = $this->repository->getAllByEnterpriseWithDefaults($enterpriseId);

                return response()->json(['movements' => $movements, 'message' => 'Movimentação atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar movimentação: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
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

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }
}
