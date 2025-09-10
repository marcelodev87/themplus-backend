<?php

namespace App\Http\Controllers;

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
            $cells = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['members']);

            return response()->json(['cells' => $cells], 200);
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

                $cells = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['members']);

                return response()->json(['cells' => $cells, 'message' => 'Célula cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar célula');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar célula: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function udpate(Request $request)
    {
        try {
            DB::beginTransaction();
            $cell = $this->service->update($request);

            if ($cell) {
                DB::commit();

                $cells = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['members']);

                return response()->json(['cells' => $cells, 'message' => 'Célula atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar célula');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar célula: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $cell = $this->repository->delete($id);

            if ($cell) {
                DB::commit();

                return response()->json(['message' => 'Célula excluída com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar célula');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar célula: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
