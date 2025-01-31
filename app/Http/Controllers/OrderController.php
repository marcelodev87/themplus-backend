<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\RegisterHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SettingsCounterRepository;
use App\Rules\OrderRule;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController
{
    private $repository;

    private $rule;

    private $service;

    private $enterpriseRepository;

    private $financialRepository;

    private $settingsCounterRepository;

    public function __construct(OrderRepository $repository, OrderRule $rule, OrderService $service, EnterpriseRepository $enterpriseRepository, FinancialRepository $financialRepository, SettingsCounterRepository $settingsCounterRepository)
    {
        $this->repository = $repository;
        $this->rule = $rule;
        $this->service = $service;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->financialRepository = $financialRepository;
        $this->settingsCounterRepository = $settingsCounterRepository;
    }

    public function indexBonds(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;

            $bonds = $this->enterpriseRepository->getBonds($request->user()->enterprise_id);
            $bonds = $bonds->map(function ($bond) {
                $bond->no_verified = $this->financialRepository->countNoVerified($bond->id);
                $bond->manage_users = $this->settingsCounterRepository->verifyAllowManage($bond->id);

                return $bond;
            });

            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['bonds' => $bonds, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os vínculos: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function indexViewCounter(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $orders = $this->repository->getAllByCounter($request->user()->enterprise_id);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['orders' => $orders, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as solicitações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function indexViewClient(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $orders = $this->repository->getAllByUser($request->user()->enterprise_id);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['orders' => $orders, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as solicitações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $order = $this->service->create($request);
            if ($order) {
                DB::commit();

                $orders = $this->repository->getAllByCounter($request->user()->enterprise_id);

                return response()->json(['orders' => $orders, 'message' => 'Solicitação enviada com sucesso'], 201);
            }

            throw new \Exception('Falha ao enviar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao enviar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $order = $this->service->update($request);

            if ($order) {
                DB::commit();

                $orders = $this->repository->getAllByCounter($request->user()->enterprise_id);

                return response()->json(['orders' => $orders, 'message' => 'Solicitação atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function actionClient(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->rule->actionClient($request);
            $orderData = $this->repository->findByIdWithCounter($request->input('id'));
            $this->service->bindCounter($request);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'invite',
                'order',
                $request->input('status').'|'.$orderData->counter->name
            );

            if ($register) {
                DB::commit();

                $orders = $this->repository->getAllByUser($request->user()->enterprise_id);
                $enteprise = $this->enterpriseRepository->findById($request->user()->enterprise_id);
                $message = $request->input('status') === 'accepted' ? 'Solicitação aceita com sucesso' : 'Solicitação rejeitada com sucesso';

                return response()->json(['orders' => $orders, 'counter' => $enteprise->counter_enterprise_id, 'message' => $message], 200);
            }

            throw new \Exception('Falha ao Aceitar/Rejeitar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao Aceitar/Rejeitar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $order = $this->repository->delete($id);

            if ($order) {
                DB::commit();

                return response()->json(['message' => 'Solicitação deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroyBond(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->deleteBond($id);
            $order = $this->repository->deleteBond($id);

            if ($order) {
                DB::commit();
                $bonds = $this->repository->getAllByCounter($request->user()->enterprise_id);

                return response()->json(['bonds' => $bonds, 'message' => 'Vínculo desfeito com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar solicitação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar solicitação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
