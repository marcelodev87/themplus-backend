<?php

namespace App\Http\Controllers;

use App\Exports\Example\MovementInsertExample;
use App\Exports\MovementExport;
use App\Helpers\EnterpriseHelper;
use App\Helpers\RegisterHelper;
use App\Http\Resources\AccountResource;
use App\Http\Resources\CategoryResource;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MovementRepository;
use App\Rules\MovementRule;
use App\Services\MovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovementController
{
    private $service;

    private $repository;

    private $categoryRepository;

    private $accountRepository;

    private $rule;

    public function __construct(
        MovementService $service,
        MovementRepository $repository,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
        MovementRule $rule
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->accountRepository = $accountRepository;
        $this->rule = $rule;
    }

    public function index(Request $request, $date)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $movements = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $date);
            $months_years = $this->repository->getMonthYears($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['movements' => $movements, 'filled_data' => $filledData, 'months_years' => $months_years], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as movimentações: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filterMovements(Request $request, $date)
    {
        try {
            $movements = $this->repository->getAllByEnterpriseWithRelationsWithParamsByDate($request, $date);
            $months_years = $this->repository->getMonthYears($request->user()->enterprise_id);

            return response()->json(['movements' => $movements, 'months_years' => $months_years], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar movimentações com base nos filtros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request, $date)
    {
        $out = filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN);
        $entry = filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN);
        $enterpriseId = $request->user()->enterprise_id;

        $movements = $this->repository->export($out, $entry, $date, $enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "movements_{$enterpriseId}_{$dateTime}.xlsx";

        return (new MovementExport($movements))->download($fileName);
    }

    public function insertExample()
    {
        $fileName = 'movements_insert_example.xlsx';

        return (new MovementInsertExample)->download($fileName);
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

            $movements = $this->service->create($request);
            foreach ($movements as $movement) {
                $movementData = $this->repository->findByIdWithRelations($movement->id);

                $register = RegisterHelper::create(
                    $request->user()->id,
                    $request->user()->enterprise_id,
                    'created',
                    'movement',
                    "{$movementData->value}|{$movementData->type}|{$movementData->account->name}|{$movementData->category->name}|{$movementData->date_movement}"
                );
            }

            if ($movements && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $movements = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $months_years = $this->repository->getMonthYears($enterpriseId);

                return response()->json(['movements' => $movements, 'message' => 'Movimentação cadastrada com sucesso', 'months_years' => $months_years], 201);
            }

            throw new \Exception('Falha ao criar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $movementData = $this->repository->findByIdWithRelations($request->input('id'));
            $movement = $this->service->update($request);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'updated',
                'movement',
                "{$movementData->value}|{$movementData->type}|{$movementData->account->name}|{$movementData->category->name}|{$movementData->date_movement}"
            );

            if ($movement && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $movements = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $months_years = $this->repository->getMonthYears($enterpriseId);

                return response()->json(['movements' => $movements, 'message' => 'Movimentação atualizada com sucesso', 'months_years' => $months_years], 200);
            }

            throw new \Exception('Falha ao atualizar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);

            $movementActual = $this->repository->findById($id);
            $movement = $this->repository->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'movement',
                "{$movementActual->value}|{$movementActual->type}|{$movementActual->account->name}|{$movementActual->category->name}|{$movementActual->date_movement}"
            );

            if ($movement && $register) {
                $this->service->updateBalanceAccount($movementActual->account_id);
                DB::commit();

                return response()->json(['message' => 'Movimentação deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar movimentação: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
