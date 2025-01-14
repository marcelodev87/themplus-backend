<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Helpers\EnterpriseHelper;
use App\Helpers\RegisterHelper;
use App\Http\Resources\OfficeResource;
use App\Http\Resources\UserResource;
use App\Repositories\EnterpriseRepository;
use App\Repositories\UserRepository;
use App\Rules\UserRule;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberController
{
    private $service;

    private $repository;

    private $rule;

    private $enterpriseRepository;

    public function __construct(UserService $service, UserRepository $repository, UserRule $rule, EnterpriseRepository $enterpriseRepository)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
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
            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'created',
                'member',
                "{$user->name}|{$user->email}"
            );

            if ($user && $register) {
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

    public function startOfficeNewUser(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->service->startOfficeNewUser($request);

            if ($user) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $offices = $this->enterpriseRepository->getAllOfficesByEnterprise($enterpriseId);

                return response()->json(['offices' => OfficeResource::collection($offices), 'message' => 'Membro adicionado á filial com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar membro para filial');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar membro da filial: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $enterpriseId = $request->user()->view_enterprise_id;

        $users = $this->repository->getAllByEnterpriseWithRelations($enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "users_{$enterpriseId}_{$dateTime}.xlsx";

        return (new UserExport($users))->download($fileName);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $member = $this->repository->findById($request->input('id'));
            $user = $this->service->updateMember($request);
            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'member',
                "{$member->name}|{$member->email}"
            );

            if ($user && $register) {
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

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $memberDelete = $this->repository->findById($id);
            $member = $this->repository->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'member',
                "{$memberDelete->name}|{$memberDelete->email}"
            );

            if ($member && $register) {
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
