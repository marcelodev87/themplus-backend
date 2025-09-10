<?php

namespace App\Http\Controllers;

use App\Repositories\CellMemberRepository;
use App\Rules\CellMemberRule;
use App\Services\CellMemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CellMemberController
{
    private $service;

    private $repository;

    private $rule;

    public function __construct(CellMemberService $service, CellMemberRepository $repository, CellMemberRule $rule)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $member = $this->service->create($request);

            if ($member) {
                DB::commit();

                $members = $this->repository->getAllByEnterprise($request->input('cell_id'), ['members']);

                return response()->json(['members' => $members, 'message' => 'Membro cadastrado com sucesso'], 201);
            }

            throw new \Exception('Falha ao cadastrar membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();

            $member = $this->repository->delete($request->input('cellID'), $request->input('memberID'));

            if ($member) {
                DB::commit();

                return response()->json(['message' => 'Membro removido com sucesso'], 200);
            }

            throw new \Exception('Falha ao remover membro');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao remover membro: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
