<?php

namespace App\Http\Controllers;

use App\Helpers\RegisterHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\MovementRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SettingsCounterRepository;
use App\Repositories\UserRepository;
use App\Rules\ReportRule;
use App\Services\MovementService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportController
{
    private $service;

    protected $rule;

    private $financialRepository;

    private $movementRepository;

    private $movementService;

    private $enterpriseRepository;

    private $settingsCounterRepository;

    private $notificationRepository;

    private $userRepository;

    public function __construct(ReportService $service, FinancialRepository $financialRepository, ReportRule $rule, MovementRepository $movementRepository, EnterpriseRepository $enterpriseRepository, SettingsCounterRepository $settingsCounterRepository, NotificationRepository $notificationRepository, UserRepository $userRepository, MovementService $movementService)
    {
        $this->service = $service;
        $this->rule = $rule;
        $this->financialRepository = $financialRepository;
        $this->movementRepository = $movementRepository;
        $this->settingsCounterRepository = $settingsCounterRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->movementService = $movementService;
    }

    public function index(Request $request, $id)
    {
        try {
            $data = $this->service->index($request, $id);
            $enterprise = $this->enterpriseRepository->findById($id);
            $permissions = $this->settingsCounterRepository->getByEnterprise($id);

            return response()->json([
                'reports' => $data['reports'],
                'enterprise_inspected' => $enterprise,
                'client_name' => $data['client_name'],
                'permissions' => $permissions,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar relatório do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function details($id)
    {
        try {
            $this->rule->details($id);

            $financial = $this->financialRepository->findById($id);
            $month = str_pad($financial->month, 2, '0', STR_PAD_LEFT);
            $data = "{$month}-{$financial->year}";
            $movements = $this->movementRepository->getAllByEnterpriseWithRelationsByDate($financial->enterprise_id, $data);
            $permissions = $permissions = $this->settingsCounterRepository->getByEnterprise($financial->enterprise_id);
            $finalized = $financial->check_counter !== null ? true : false;

            return response()->json([
                'movements' => $movements,
                'permissions' => $permissions,
                'finalized' => $finalized,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar detalhes do relatório do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $report = $this->financialRepository->findById($id);
            $data = $this->service->finalize($request, $id);
            $enterpriseName = $report->enterprise->name;

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'finalized',
                'report',
                "$report->month/$report->year|$enterpriseName"
            );

            if ($data && $register) {
                DB::commit();

                $result = $this->service->index($request, $report->enterprise_id);

                return response()->json([
                    'reports' => $result['reports'],
                    'client_name' => $result['client_name'],
                    'message' => 'Relatório finalizado com sucesso',
                ], 200);
            }
            throw new \Exception('Falha ao finalizar relatório');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao finalizar relatório do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reopen(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->reopen($id);
            $report = $this->financialRepository->findById($id);
            $this->financialRepository->delete($id);

            $enterpriseName = $report->enterprise->name;

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'reopen',
                'report',
                "$report->month/$report->year|$enterpriseName"
            );

            if ($report && $register) {
                DB::commit();

                $result = $this->service->index($request, $report->enterprise_id);

                return response()->json([
                    'reports' => $result['reports'],
                    'client_name' => $result['client_name'],
                    'message' => 'Relatório reaberto com sucesso',
                ], 200);
            }
            throw new \Exception('Falha ao reverter relatório para não verificado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao reverter relatório para não verificado do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function undo(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $financial = $this->financialRepository->findById($id);
            $report = $this->financialRepository->update($id, ['check_counter' => null]);
            $enterpriseName = $financial->enterprise->name;

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'undone',
                'report',
                "$report->month/$report->year|$enterpriseName"
            );

            if ($report && $register) {
                DB::commit();

                $result = $this->service->index($request, $report->enterprise_id);

                return response()->json([
                    'reports' => $result['reports'],
                    'client_name' => $result['client_name'],
                    'message' => 'Verificação de relatório removida com sucesso',
                ], 200);
            }
            throw new \Exception('Falha ao reverter relatório para não verificado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao reverter relatório para não verificado do cliente: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroyMovement(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->destroyMovement($id);

            $movementActual = $this->movementRepository->findById($id);

            if ($movementActual->receipt) {
                $oldFilePath = str_replace(env('AWS_URL').'/', '', $movementActual->receipt);
                Storage::disk('s3')->delete($oldFilePath);
            }

            $movement = $this->movementRepository->delete($id);

            $users = $this->userRepository->getAllByEnterprise($movementActual->enterprise_id);
            $formattedDate = Carbon::parse($movementActual->date_movement)->format('d/m/Y');
            $formattedValue = number_format($movementActual->value, 2, ',', '.');

            foreach ($users as $user) {
                $dataNotification = [
                    'user_id' => $user->id,
                    'enterprise_id' => $user->enterprise_id,
                    'title' => 'Exclusão de movimentação',
                    'text' => "Uma movimentação do período $formattedDate, do tipo $movementActual->type e valor R$ $formattedValue foi deletada pela organização de contabilidade.",
                ];
                $this->notificationRepository->createForUser($dataNotification);
            }

            $permissions = $permissions = $this->settingsCounterRepository->getByEnterprise($movementActual->enterprise_id);
            $enterprise = $this->enterpriseRepository->findById($movementActual->enterprise_id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'manageMovement',
                "$movementActual->value|$movementActual->type|$movementActual->date_movement|$enterprise->name|$enterprise->email"
            );

            if ($movement && $register) {
                DB::commit();

                return response()->json([
                    'message' => 'Movimentação deletada com sucesso',
                    'permissions' => $permissions,
                ], 200);
            }

            throw new \Exception('Falha ao deletar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateMovementByCounter(Request $request)
    {
        try {
            DB::beginTransaction();
            $movementData = $this->movementRepository->findById($request->input('id'));
            $movement = $this->movementService->updateMovementByCounter($request);

            $users = $this->userRepository->getAllByEnterprise($movementData->enterprise_id);
            $formattedDate = Carbon::parse($movementData->date_movement)->format('d/m/Y');
            $formattedValue = number_format($movementData->value, 2, ',', '.');

            foreach ($users as $user) {
                $dataNotification = [
                    'user_id' => $user->id,
                    'enterprise_id' => $user->enterprise_id,
                    'title' => 'Edição de movimentação',
                    'text' => "Uma movimentação do período $formattedDate, do tipo $movementData->type e valor R$ $formattedValue foi atualizada pela organização de contabilidade.",
                ];
                $this->notificationRepository->createForUser($dataNotification);
            }

            $enterprise = $this->enterpriseRepository->findById($movementData->enterprise_id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'manageMovement',
                "$movementData->value|$movementData->type|$movementData->date_movement|$enterprise->name|$enterprise->email"
            );

            if ($movement && $register) {
                DB::commit();

                $dateObject = Carbon::createFromFormat('Y-m-d', $movementData->date_movement);
                $data = $dateObject->format('m-Y');

                $movements = $this->movementRepository->getAllByEnterpriseWithRelationsByDate($movementData->enterprise_id, $data);
                $permissions = $permissions = $this->settingsCounterRepository->getByEnterprise($movementData->enterprise_id);

                return response()->json([
                    'movements' => $movements,
                    'permissions' => $permissions,
                    'message' => 'Movimentação atualizada com sucesso',
                ], 200);
            }

            throw new \Exception('Falha ao atualizar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
