<?php

namespace App\Http\Controllers;

use App\Repositories\DepartmentRepository;
use App\Rules\DepartmentRule;
use App\Services\DepartmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(DepartmentService $service, DepartmentRepository $repository, DepartmentRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $departments = $this->repository->getAllByEnterprise($enterpriseId);

            return response()->json(['departments' => $departments], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os departamentos: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $department = $this->service->create($request);

            if ($department) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $departments = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['departments' => $departments, 'message' => 'Departamento cadastrado com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar departamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar departamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $department = $this->service->update($request);

            if ($department) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $departments = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['departments' => $departments, 'message' => 'Departamento atualizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar departamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar departamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id, Request $request)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $department = $this->repository->delete($id);

            if ($department) {
                DB::commit();
                $enterpriseId = $request->user()->enterprise_id;
                $departments = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['departments' => $departments, 'message' => 'Departamento deletado com sucesso'], 200);

            }

            throw new \Exception('Falha ao deletar departamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar departamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
