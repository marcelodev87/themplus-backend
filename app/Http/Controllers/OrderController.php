<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Helpers\RegisterHelper;
use App\Repositories\CategoryRepository;
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

    private $categoryRepository;

    public function __construct(OrderRepository $repository, OrderRule $rule, OrderService $service, EnterpriseRepository $enterpriseRepository, FinancialRepository $financialRepository, SettingsCounterRepository $settingsCounterRepository, CategoryRepository $categoryRepository)
    {
        $this->repository = $repository;
        $this->rule = $rule;
        $this->service = $service;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->financialRepository = $financialRepository;
        $this->categoryRepository = $categoryRepository;
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
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json(['bonds' => $bonds, 'filled_data' => $filledData, 'notifications' => $notifications], 200);
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
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json(['orders' => $orders, 'filled_data' => $filledData, 'notifications' => $notifications], 200);
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
            $enterprise = $this->enterpriseRepository->findById($request->input('enterpriseId'));

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'created',
                'order',
                "$enterprise->name|$enterprise->email"
            );
            if ($order && $register) {
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
            $log = $this->repository->findById($request->input('id'));
            $enterprise = $this->enterpriseRepository->findById($log->enterprise_id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'order',
                "$enterprise->name|$enterprise->email"
            );

            if ($order && $register) {
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

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $log = $this->repository->findById($id);
            $enterprise = $this->enterpriseRepository->findById($log->enterprise_id);

            $order = $this->repository->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'order',
                "$enterprise->name|$enterprise->email"
            );

            if ($order && $register) {
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
            $this->categoryRepository->removeAlert($id);
            $order = $this->repository->deleteBond($id);
            $enterprise = $this->enterpriseRepository->findById($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'bond',
                "$enterprise->name|$enterprise->email"
            );

            if ($order && $register) {
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
