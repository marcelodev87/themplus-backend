<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\MemberHelper;
use App\Http\Resources\MemberChurchResource;
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
            $members = $this->repository->getAllByEnterprise(
                $request->user()->enterprise_id,
                ['roles', 'ministries', 'family'],
                $request->has('active') ? (int) $request->query('active') : null
            );
            $filledData = EnterpriseHelper::filledData($request->user()->enterprise_id);

            return response()->json(['members' => $members, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os membros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $member = $this->repository->findById($request['memberID']);

            return response()->json(['member' => new MemberChurchResource($member)], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function active(Request $request)
    {
        try {
            $activeValue = $request->input('active');

            if (! in_array($activeValue, [1, 0], true)) {
                return response()->json([
                    'message' => 'O valor de ativação deve ser estritamente o número 1 (ativo) ou 0 (inativo).',
                ], 422);
            }

            DB::beginTransaction();

            if ($activeValue === 0) {
                MemberHelper::hasLink($request->user()->enterprise_id, $request->input('userId'));
            }

            $member = $this->repository->update($request->input('userId'), ['active' => $activeValue]);

            if ($member) {
                DB::commit();

                $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles', 'ministries', 'family']);

                $message = $activeValue == 0 ? 'Membro inativado com sucesso' : 'Membro ativado com sucesso';

                return response()->json(['members' => $members, 'message' => $message], 200);
            }

            throw new \Exception('Falha ao atualizar dados do membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar dados do membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            MemberHelper::allowCreateMember($request->user()->enterprise_id);

            $member = $this->service->create($request);

            if ($member) {
                DB::commit();

                $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles', 'ministries', 'family']);

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

                $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles', 'ministries', 'family']);

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
                $members = $this->repository->getAllByEnterprise($request->user()->enterprise_id, ['roles', 'ministries', 'family']);

                return response()->json(['members' => $members, 'message' => 'Membro excluído com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteRelationship(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->rule->deleteRelationship($request);
            $relationship = $this->repository->deleteRelationship($request['memberID'], $request['relatedMemberID'], $request['relationshipID']);

            if ($relationship) {
                DB::commit();

                return response()->json(['message' => 'Relação excluída com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar relação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar relação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
