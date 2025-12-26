<?php

namespace App\Http\Controllers;

use App\Repositories\PreRegistrationConfigRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreRegistrationConfigController
{
    private $repository;

    public function __construct(PreRegistrationConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $config = $this->repository->getByEnterprise($request->user()->enterprise_id);

            return response()->json(['config' => $config], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar configurações do formulário de membros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function check(string $id)
    {
        try {
            $config = $this->repository->getByEnterprise($id);

            if (! $config) {
                return response()->json([
                    'active' => false,
                    'message' => 'Configuração não encontrada'
                ], 404);
            }

            return response()->json([
                'active' => (bool) $config->active
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Erro ao validar formulário: ' . $e->getMessage());

            return response()->json([
                'message' => 'Erro interno ao validar formulário'
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $active = $request->input('active');

            if (! in_array($active, [0, 1], true)) {
                return response()->json([
                    'message' => 'O campo active deve ser 0 ou 1',
                ], 422);
            }

            $config = $this->repository->updateByEnterpriseId(
                $request->user()->enterprise_id,
                ['active' => $active]
            );

            DB::commit();

            return response()->json([
                'config' => $config,
                'message' => 'Configuração atualizada com sucesso',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar configuração do formulário de membros: '.$e->getMessage());

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
