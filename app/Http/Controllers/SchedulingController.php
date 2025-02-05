<?php

namespace App\Http\Controllers;

use App\Exports\SchedulingExport;
use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Helpers\RegisterHelper;
use App\Http\Resources\AccountResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategorySelect;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SchedulingRepository;
use App\Rules\SchedulingRule;
use App\Services\SchedulingService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SchedulingController
{
    private $service;

    private $repository;

    private $rule;

    private $categoryRepository;

    private $accountRepository;

    public function __construct(
        SchedulingService $service,
        SchedulingRepository $repository,
        SchedulingRule $rule,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
        $this->categoryRepository = $categoryRepository;
        $this->accountRepository = $accountRepository;
    }

    public function index(Request $request, $date)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
            $schedulings = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $date);
            $monthsYears = $this->repository->getMonthYears($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaults($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json([
                'schedulings' => $schedulings,
                'filled_data' => $filledData,
                'months_years' => $monthsYears,
                'notifications' => $notifications,
                'categories' => CategorySelect::collection($categories),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os agendamentos: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filterSchedulings(Request $request, $date)
    {
        try {
            $schedulings = $this->repository->getAllByEnterpriseWithRelationsWithParamsByDate($request, $date);
            $monthsYears = $this->repository->getMonthYears($request->user()->view_enterprise_id);
            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaults($request->user()->view_enterprise_id);

            return response()->json([
                'schedulings' => $schedulings,
                'months_years' => $monthsYears,
                'categories' => CategorySelect::collection($categories),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar agendamentos com base nos filtros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request, $date)
    {
        $out = filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN);
        $entry = filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN);
        $expired = filter_var($request->query('expired'), FILTER_VALIDATE_BOOLEAN);
        $categoryId = ($request->query('category') === 'null') ? null : $request->query('category');
        $enterpriseId = $request->user()->view_enterprise_id;

        $schedulings = $this->repository->export($out, $entry, $expired, $date, $categoryId, $enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "schedulings_{$enterpriseId}_{$dateTime}.xlsx";

        return (new SchedulingExport($schedulings))->download($fileName);
    }

    public function exportPDF(Request $request, $date)
    {
        $out = filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN);
        $entry = filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN);
        $expired = filter_var($request->query('expired'), FILTER_VALIDATE_BOOLEAN);
        $categoryId = ($request->query('category') === 'null') ? null : $request->query('category');
        $enterpriseId = $request->user()->view_enterprise_id;

        $schedulings = $this->repository->export($out, $entry, $expired, $date, $categoryId, $enterpriseId);
        $schedulings = collect($schedulings)->sortByDesc('date_movement');

        $pdf = PDF::loadView('schedulings.pdf', [
            'schedulings' => $schedulings,
            'date' => $date,
        ]);

        return $pdf->download('schedulings.pdf');
    }

    public function getFormInformations(Request $request, $type)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;

            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaultsOnlyActive($enterpriseId, $type);
            $accounts = $this->accountRepository->getAllByEnterpriseOnlyActive($enterpriseId);

            return response()->json([
                'categories' => CategoryResource::collection($categories),
                'accounts' => AccountResource::collection($accounts),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $schedulings = $this->service->create($request);

            foreach ($schedulings as $scheduling) {
                $schedulingData = $this->repository->findByIdWithRelations($scheduling->id);
                $register = RegisterHelper::create(
                    $request->user()->id,
                    $request->user()->enterprise_id,
                    'created',
                    'scheduling',
                    "{$schedulingData->value}|{$schedulingData->type}|{$schedulingData->account->name}|{$schedulingData->account->account_number}|{$schedulingData->account->agency_number}|{$schedulingData->category->name}|{$schedulingData->date_movement}"
                );
            }

            if ($schedulings && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $schedulings = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $monthsYears = $this->repository->getMonthYears($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento cadastrado com sucesso', 'months_years' => $monthsYears], 201);
            }

            throw new \Exception('Falha ao criar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $schedulingData = $this->repository->findByIdWithRelations($request->input('id'));
            $scheduling = $this->service->update($request);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'scheduling',
                "{$schedulingData->value}|{$schedulingData->type}|{$schedulingData->account->name}|{$schedulingData->account->account_number}|{$schedulingData->account->agency_number}|{$schedulingData->category->name}|{$schedulingData->date_movement}"
            );

            if ($scheduling && $register) {
                DB::commit();
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $enterpriseId = $request->user()->enterprise_id;
                $schedulings = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $monthsYears = $this->repository->getMonthYears($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento atualizado com sucesso', 'months_years' => $monthsYears], 200);
            }

            throw new \Exception('Falha ao atualizar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->finalize($id);
            $schedulingData = $this->repository->findByIdWithRelations($id);
            $movement = $this->repository->finalize($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'finalize',
                'scheduling',
                "{$schedulingData->value}|{$schedulingData->type}|{$schedulingData->account->name}|{$schedulingData->account->account_number}|{$schedulingData->account->agency_number}|{$schedulingData->category->name}|{$schedulingData->date_movement}"
            );

            if ($movement && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $schedulings = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $monthsYears = $this->repository->getMonthYears($enterpriseId);

                return response()->json(['schedulings' => $schedulings, 'message' => 'Agendamento finalizado com sucesso', 'months_years' => $monthsYears], 200);
            }

            throw new \Exception('Falha ao finalizar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao finalizar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);

            $schedulingActual = $this->repository->findById($id);
            if ($schedulingActual->receipt) {
                $oldFilePath = str_replace(env('AWS_URL').'/', '', $schedulingActual->receipt);
                Storage::disk('s3')->delete($oldFilePath);
            }

            $scheduling = $this->repository->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'scheduling',
                "{$schedulingActual->value}|{$schedulingActual->type}|{$schedulingActual->account->name}|{$schedulingActual->account->account_number}|{$schedulingActual->account->agency_number}|{$schedulingActual->category->name}|{$schedulingActual->date_movement}"
            );

            if ($scheduling && $register) {
                DB::commit();

                return response()->json(['message' => 'Agendamento deletado com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar agendamento');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar agendamento: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
