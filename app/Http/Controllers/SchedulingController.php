<?php

namespace App\Http\Controllers;

use App\Services\SchedulingRepository;
use App\Services\SchedulingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchedulingController
{
    private $service;

    private $repository;

    public function __construct(
        SchedulingService $service,
        SchedulingRepository $repository,
    ) {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $schedulings = $this->repository->getAllByEnterprise($enterpriseId);

            return response()->json(['schedulings' => $schedulings], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os agendamentos: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
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
                $schedulings = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento cadastrado com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar agendamento: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
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
                $schedulings = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento atualizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar agendamento: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
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

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }
}
