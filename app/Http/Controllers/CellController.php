<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\CellRepository;
use App\Rules\CellRule;
use App\Services\CellService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CellController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(CellService $service, CellRepository $repository, CellRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $cells = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['host', 'leader']);
            $filledData = EnterpriseHelper::filledData($request->user()->enterprise_id);

            return response()->json(['filled_data' => $filledData, 'cells' => $cells], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as células: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $cell = $this->service->create($request);

            if ($cell) {
                DB::commit();

                $cells = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['network', 'leader']);

                return response()->json(['cells' => $cells, 'message' => 'Célula cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar célula');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar célula: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $cell = $this->service->update($request);

            if ($cell) {
                DB::commit();

                $cells = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['network', 'leader']);

                return response()->json(['cells' => $cells, 'message' => 'Célula atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar célula');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar célula: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $cell = $this->repository->delete($id);

            if ($cell) {
                DB::commit();

                $cells = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['network', 'leader']);

                return response()->json(['cells' => $cells, 'message' => 'Célula excluída com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar célula');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar célula: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
