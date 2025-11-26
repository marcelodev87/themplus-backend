<?php

namespace App\Http\Controllers;

use App\Repositories\RelationshipRepository;
use App\Rules\RelationshipRule;
use App\Services\RelationshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RelationshipController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(RelationshipService $service, RelationshipRepository $repository, RelationshipRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
            $relationships = $this->repository->getAllByEnterprise($enterpriseId);

            return response()->json(['relationships' => $relationships], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as relações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $relationship = $this->service->create($request);

            if ($relationship) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $relationships = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['relationships' => $relationships, 'message' => 'Relação cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar relação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar relação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $relationship = $this->service->update($request);

            if ($relationship) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $relationships = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['relationships' => $relationships, 'message' => 'Relação atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar relação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar relação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);

            $relationship = $this->repository->findById($id);
            if ($relationship->default === 1) {
                throw ValidationException::withMessages([
                    'default' => ['Não pode excluir relação padrão do sistema'],
                ]);
            }

            $relationship = $this->repository->delete($id);

            if ($relationship) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $relationships = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['relationships' => $relationships, 'message' => 'Relação deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar relação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar relação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
