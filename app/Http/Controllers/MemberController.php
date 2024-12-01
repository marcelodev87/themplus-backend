<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Helpers\EnterpriseHelper;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberController
{
    private $service;

    private $repository;

    public function __construct(UserService $service, UserRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['users' => UserResource::collection($users), 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os membros da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->include($request);

            if ($user) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['users' => UserResource::collection($users), 'message' => 'Membro adicionado á sua organização com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar membro da organização');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar membro da organização: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $enterpriseId = $request->user()->enterprise_id;

        $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "users_{$enterpriseId}_{$dateTime}.xlsx";

        return (new UserExport($users))->download($fileName);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $this->service->updateMember($request);

            if ($user) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

                return response()->json(['users' => UserResource::collection($users), 'message' => 'Dados do membro foram atualizados com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar dados do membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar dados do membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $member = $this->repository->delete($id);

            if ($member) {
                DB::commit();

                return response()->json(['message' => 'Membro deletado com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
