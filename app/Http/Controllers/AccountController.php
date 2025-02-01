<?php

namespace App\Http\Controllers;

use App\Exports\AccountExport;
use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Helpers\RegisterHelper;
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
            $enterpriseId = $request->user()->view_enterprise_id;
            $accounts = $this->repository->getAllByEnterprise($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json(['accounts' => $accounts, 'filled_data' => $filledData, 'notifications' => $notifications], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as contas: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $account = $this->service->create($request);
            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'created',
                'account',
                "{$account->name}|{$account->account_number}|{$account->agency_number}"
            );

            if ($account && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => 'Conta cadastrada com sucesso'], 201);
            }

            throw new \Exception('Falha ao criar conta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar conta: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $enterpriseId = $request->user()->view_enterprise_id;

        $accounts = $this->repository->getAllByEnterprise($enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "accounts_{$enterpriseId}_{$dateTime}.xlsx";

        return (new AccountExport($accounts))->download($fileName);
    }

    public function createTransfer(Request $request)
    {
        try {
            DB::beginTransaction();
            $transfer = $this->service->createTransfer($request);

            $accountNameEntry = $this->repository->findById($request->input('accountEntry'));
            $accountNameOut = $this->repository->findById($request->input('accountOut'));

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'transfer',
                'account',
                "{$accountNameOut->name}|{$accountNameOut->account_number}|{$accountNameOut->agency_number}|{$accountNameEntry->name}|{$accountNameEntry->account_number}|{$accountNameEntry->agency_number}|{$request->input('value')}"
            );

            if ($transfer['out'] && $transfer['entry'] && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => 'Transferência realizada com sucesso'], 201);
            }

            throw new \Exception('Falha ao realizar transferência');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao realizar transferência: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $accountData = $this->repository->findById($request->input('id'));
            $account = $this->service->update($request);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'account',
                "{$accountData->name}|{$accountData->account_number}|{$accountData->agency_number}"
            );

            if ($account && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => 'Conta atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar conta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar conta: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function active(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $account = $this->service->updateActive($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'reactivated',
                'account',
                "{$account->name}|{$account->account_number}|{$account->agency_number}"
            );

            if ($account && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => 'Conta reativada com sucesso'], 200);
            }

            throw new \Exception('Falha ao reativar conta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao reativar conta: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $accountData = $this->repository->findById($id);
            $account = $this->service->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                $account['inactivated'] ? 'inactivated' : 'deleted',
                'account',
                "{$accountData->name}|{$accountData->account_number}|{$accountData->agency_number}"
            );

            if ($account['data'] && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $accounts = $this->repository->getAllByEnterprise($enterpriseId);

                return response()->json(['accounts' => $accounts, 'message' => $account['message']], 200);
            }

            throw new \Exception('Falha ao deletar conta');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar conta: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
