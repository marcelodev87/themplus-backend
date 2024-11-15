<?php

namespace App\Http\Controllers;

use App\Repositories\AccountRepository;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController
{
    private $service;

    private $repository;

    public function __construct(AccountService $service, AccountRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $accounts = $this->repository->getAllByEnterprise($enterpriseId);

            return response()->json(['accounts' => $accounts], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as contas: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $account = $this->service->create($request);

            if ($account) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => 'Conta cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar conta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar conta: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }

    public function createTransfer(Request $request)
    {
        try {
            DB::beginTransaction();
            $transfer = $this->service->createTransfer($request);

            if ($transfer['out'] && $transfer['entry']) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => 'Transferência realizada com sucesso'], 201);
            }

            throw new \Exception('Falha ao realizar transferência');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao realizar transferência: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $account = $this->service->update($request);

            if ($account) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => 'Conta atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar conta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar conta: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $category = $this->repository->delete($id);

            if ($category) {
                DB::commit();

                return response()->json(['message' => 'Conta deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar conta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar conta: '.$e->getMessage());

            return response()->json(['message' => 'Houve erro: '.$e->getMessage()], 500);
        }
    }
}
