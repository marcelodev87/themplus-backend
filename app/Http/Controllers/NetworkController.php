<?php

namespace App\Http\Controllers;

use App\Repositories\NetworkRepository;
use App\Rules\NetworkRule;
use App\Services\NetworkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NetworkController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(NetworkService $service, NetworkRepository $repository, NetworkRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $networks = $this->repository->getAllByEnterprise($request->user()->enterprise_id);

            return response()->json(['networks' => $networks], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as redes: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $network = $this->service->create($request);

            if ($network) {
                DB::commit();

                $networks = $this->repository->getAllByEnterprise($request->user()->enterprise_id);

                return response()->json(['networks' => $networks, 'message' => 'Rede cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar rede');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar rede: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function udpate(Request $request)
    {
        try {
            DB::beginTransaction();
            $network = $this->service->update($request);

            if ($network) {
                DB::commit();

                 $networks = $this->repository->getAllByEnterprise($request->user()->enterprise_id);

                return response()->json(['networks' => $networks, 'message' => 'Rede atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar rede');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar rede: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $network = $this->repository->delete($id);

            if ($network) {
                DB::commit();

                return response()->json(['message' => 'Rede excluída com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar rede');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar rede: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
