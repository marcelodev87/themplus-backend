<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\MemberRepository;
use App\Rules\MemberRule;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberChurchController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(MemberService $service, MemberRepository $repository, MemberRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles']);
            $filledData = EnterpriseHelper::filledData($request->user()->enterprise_id);

            return response()->json(['members' => $members, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os membros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $member = $this->service->create($request);

            if ($member) {
                DB::commit();

                $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles']);

                return response()->json(['members' => $members, 'message' => 'Membro cadastrado com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $member = $this->service->update($request);

            if ($member) {
                DB::commit();

                $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles']);

                return response()->json(['members' => $members, 'message' => 'Membro atualizado com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $member = $this->repository->delete($id);

            if ($member) {
                DB::commit();
                $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles']);

                return response()->json(['members' => $members, 'message' => 'Membro excluído com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
