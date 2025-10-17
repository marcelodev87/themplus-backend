<?php

namespace App\Http\Controllers;

use App\Repositories\RoleRepository;
use App\Rules\RoleRule;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController
{
    private $service;

    private $repository;

    private $enterpriseRepository;

    private $rule;

    public function __construct(RoleService $service, RoleRepository $repository, RoleRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $roles = $this->repository->getAllByEnterprise($request->user()->enterprise_id);

            return response()->json(['roles' => $roles], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os cargos: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->service->create($request);

            if ($role) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $roles = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['roles' => $roles, 'message' => 'Cargo cadastrado com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar cargo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar cargo: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $role = $this->service->update($request);

            if ($role) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $roles = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['roles' => $roles, 'message' => 'Cargo atualizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar cargo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar cargo: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $role = $this->repository->delete($id);

            if ($role) {
                DB::commit();

                $roles = $this->repository->getAllByEnterprise($request->user()->enterprise_id);

                return response()->json(['roles' => $roles, 'message' => 'Cargo excluído com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar cargo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar cargo: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
