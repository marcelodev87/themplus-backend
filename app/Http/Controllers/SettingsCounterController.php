<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\RegisterHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SettingsCounterRepository;
use App\Rules\SettingsCounterRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsCounterController
{
    private $repository;

    private $rule;

    private $enterpriseRepository;

    private $orderRepository;

    private $financialRepository;

    public function __construct(SettingsCounterRepository $repository, SettingsCounterRule $rule, FinancialRepository $financialRepository, EnterpriseRepository $enterpriseRepository, OrderRepository $orderRepository)
    {
        $this->repository = $repository;
        $this->rule = $rule;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->orderRepository = $orderRepository;
        $this->financialRepository = $financialRepository;
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $settings = $this->repository->getByEnterprise($enterpriseId);

            return response()->json(['settings' => $settings], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar as configurações de contabilidade: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->rule->update($request);
            $data = [
                'allow_add_user' => $request->input('allowAddUser'),
                'allow_edit_user' => $request->input('allowEditUser'),
                'allow_delete_user' => $request->input('allowDeleteUser'),
                'allow_edit_movement' => $request->input('allowEditMovement'),
                'allow_delete_movement' => $request->input('allowDeleteMovement'),
            ];

            $setting = $this->repository->update($request->user()->enterprise_id, $data);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'permission',
                '-'
            );

            if ($setting && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $deliveries = $this->financialRepository->mountDeliveries($enterpriseId);
                $enterprise = $this->enterpriseRepository->findById($request->user()->view_enterprise_id);
                $orderCount = $this->orderRepository->getAllByUser($request->user()->enterprise_id)->count();
                $filledData = EnterpriseHelper::filledData($enterpriseId);

                return response()->json([
                    'deliveries' => $deliveries,
                    'counter' => $enterprise->counter_enterprise_id,
                    'filled_data' => $filledData,
                    'order_count' => $orderCount,
                    'is_headquarters' => $enterprise->created_by === null ? true : false,
                    'message' => 'Configurações salvas com sucesso',
                ], 200);
            }

            throw new \Exception('Falha ao atualizar configurações');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar configurações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
