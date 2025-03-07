<?php

namespace App\Http\Controllers;

use App\Exports\Example\MovementInsertExample;
use App\Exports\MovementExport;
use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Helpers\RegisterHelper;
use App\Http\Resources\AccountResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategorySelect;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\MovementAnalyzeRepository;
use App\Repositories\MovementRepository;
use App\Rules\MovementRule;
use App\Services\MovementService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MovementController
{
    private $service;

    private $repository;

    private $categoryRepository;

    private $accountRepository;

    private $enterpriseRepository;

    private $financialRepository;
    private $movementAnalyzeRepository;

    private $rule;

    public function __construct(
        MovementService $service,
        MovementRepository $repository,
        AccountRepository $accountRepository,
        CategoryRepository $categoryRepository,
        EnterpriseRepository $enterpriseRepository,
        FinancialRepository $financialRepository,
        MovementAnalyzeRepository $movementAnalyzeRepository,
        MovementRule $rule
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->accountRepository = $accountRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->financialRepository = $financialRepository;
        $this->movementAnalyzeRepository = $movementAnalyzeRepository;
        $this->rule = $rule;
    }

    public function index(Request $request, $date)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;

            $movements = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $date);
            $months_years = $this->repository->getMonthYears($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $delivered = $this->repository->checkDelivered($enterpriseId, $date);
            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaults($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);
            $movements_analyze = $this->movementAnalyzeRepository->countByEnterprise($enterpriseId);

            return response()->json([
                'movements' => $movements,
                'filled_data' => $filledData,
                'months_years' => $months_years,
                'categories' => CategorySelect::collection($categories),
                'notifications' => $notifications,
                'delivered' => $delivered,
                'movements_analyze' => $movements_analyze
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as movimentações: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filterMovements(Request $request, $date)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;

            $movements = $this->repository->getAllByEnterpriseWithRelationsWithParamsByDate($request, $date);
            $months_years = $this->repository->getMonthYears($enterpriseId);
            $delivered = $this->repository->checkDelivered($enterpriseId, $date);
            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaults($enterpriseId);

            return response()->json([
                'movements' => $movements,
                'months_years' => $months_years,
                'categories' => CategorySelect::collection($categories),
                'delivered' => $delivered,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar movimentações com base nos filtros: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request, $date)
    {
        $out = filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN);
        $entry = filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN);
        $categoryId = ($request->query('category') === 'null') ? null : $request->query('category');
        $enterpriseId = $request->user()->view_enterprise_id;

        $movements = $this->repository->export($out, $entry, $date, $categoryId, $enterpriseId);

        $dateTime = now()->format('Ymd_His');
        $fileName = "movements_{$enterpriseId}_{$dateTime}.xlsx";

        return (new MovementExport($movements))->download($fileName);
    }

    public function exportPDF(Request $request, $date)
    {
        $out = filter_var($request->query('out'), FILTER_VALIDATE_BOOLEAN);
        $entry = filter_var($request->query('entry'), FILTER_VALIDATE_BOOLEAN);
        $categoryId = ($request->query('category') === 'null') ? null : $request->query('category');
        $enterpriseId = $request->user()->view_enterprise_id;

        $movements = $this->repository->export($out, $entry, $date, $categoryId, $enterpriseId);
        $movements = collect($movements)->sortByDesc('date_movement');

        $pdf = PDF::loadView('movements.pdf', [
            'movements' => $movements,
            'date' => $date,
        ]);

        return $pdf->download('movements.pdf');
    }

    public function downloadFile($file)
    {
        $filePath = storage_path("app/private/receipts/{$file}");

        if (!file_exists($filePath)) {
            throw new \Exception('Não existe este arquivo');
        }

        return response()->download($filePath, $file);
    }

    // public function insertExample()
    // {
    //     $fileName = 'movements_insert_example.xlsx';

    //     return (new MovementInsertExample)->download($fileName);
    // }
    public function insertExample()
    {
        $filePath = storage_path('app/public/exports/movements_example.xlsx');

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath);
    }

    public function insert(Request $request)
    {
        try {
            DB::beginTransaction();

            $movements = $this->service->insert($request);

            if ($movements) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $movements = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $months_years = $this->repository->getMonthYears($enterpriseId);
                $delivered = $this->repository->checkDelivered($enterpriseId, $currentDate);
                $categories = $this->categoryRepository->getAllByEnterpriseWithDefaults($enterpriseId);
                $notifications = NotificationsHelper::getNoRead($request->user()->id);

                return response()->json([
                    'movements' => $movements,
                    'months_years' => $months_years,
                    'categories' => CategorySelect::collection($categories),
                    'notifications' => $notifications,
                    'message' => 'Inserção de movimentações em lote realizada com sucesso',
                    'delivered' => $delivered,
                ], 201);
            }

            throw new \Exception('Falha ao criar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar movimentação: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getFormInformations(Request $request, $type)
    {
        try {
            $enterpriseId = $request->input('enterpriseId', $request->user()->enterprise_id);

            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaultsOnlyActive(
                $enterpriseId,
                $type === 'all' ? null : $type
            );
            $accounts = $this->accountRepository->getAllByEnterpriseOnlyActive($enterpriseId);

            return response()->json([
                'categories' => CategoryResource::collection($categories),
                'accounts' => AccountResource::collection($accounts),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações: ' . $e->getMessage());

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
                    "{$movementData->value}|{$movementData->type}|{$movementData->account->name}|{$movementData->account->account_number}|{$movementData->account->agency_number}|{$movementData->category->name}|{$movementData->date_movement}"
                );
            }

            if ($movements && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $movements = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $months_years = $this->repository->getMonthYears($enterpriseId);
                $delivered = $this->repository->checkDelivered($enterpriseId, $currentDate);

                return response()->json([
                    'movements' => $movements,
                    'message' => 'Movimentação cadastrada com sucesso',
                    'months_years' => $months_years,
                    'delivered' => $delivered,
                ], 201);
            }

            throw new \Exception('Falha ao criar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar movimentação: ' . $e->getMessage());

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
                "{$movementData->value}|{$movementData->type}|{$movementData->account->name}|{$movementData->account->account_number}|{$movementData->account->agency_number}|{$movementData->category->name}|{$movementData->date_movement}"
            );

            if ($movement && $register) {
                DB::commit();

                $enterpriseId = $request->user()->enterprise_id;
                $now = now()->setTimezone('America/Sao_Paulo');
                $currentDate = $now->format('m-Y');

                $movements = $this->repository->getAllByEnterpriseWithRelationsByDate($enterpriseId, $currentDate);
                $months_years = $this->repository->getMonthYears($enterpriseId);
                $delivered = $this->repository->checkDelivered($enterpriseId, $currentDate);

                return response()->json([
                    'movements' => $movements,
                    'message' => 'Movimentação atualizada com sucesso',
                    'months_years' => $months_years,
                    'delivered' => $delivered,
                ], 200);
            }

            throw new \Exception('Falha ao atualizar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar movimentação: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function saveObservations(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->service->saveObservations($request);

            if (count($request->movements) > 0) {
                $movement = $this->repository->findById($request->movements[0]['id']);
                $enterprise = $this->enterpriseRepository->findById($movement->enterprise_id);

                $formattedDate = Carbon::parse($movement->date_movement)->format('m/Y');
                RegisterHelper::create(
                    $request->user()->id,
                    $request->user()->enterprise_id,
                    'observations',
                    'manageMovement',
                    "{$formattedDate}|$enterprise->name|$enterprise->email"
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Observações salvas com sucesso',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao salvar observação: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function indexBonds(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;

            $bonds = $this->enterpriseRepository->getBonds($request->user()->enterprise_id);
            $bonds = $bonds->map(function ($bond) {
                $bond->no_verified = $this->financialRepository->countNoVerified($bond->id);

                return $bond;
            });

            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json(['bonds' => $bonds, 'filled_data' => $filledData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os vínculos: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);

            $movementActual = $this->repository->findById($id);

            if ($movementActual->receipt) {
                $oldFilePath = str_replace(env('AWS_URL') . '/', '', $movementActual->receipt);
                Storage::disk('s3')->delete($oldFilePath);
            }

            $movement = $this->repository->delete($id);

            $register = RegisterHelper::create(
                $request->user()->id,
                $request->user()->enterprise_id,
                'deleted',
                'movement',
                "{$movementActual->value}|{$movementActual->type}|{$movementActual->account->name}|{$movementActual->account->account_number}|{$movementActual->account->agency_number}|{$movementActual->category->name}|{$movementActual->date_movement}"
            );

            if ($movement && $register) {
                $this->service->updateBalanceAccount($movementActual->account_id);
                DB::commit();

                return response()->json(['message' => 'Movimentação deletada com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar movimentação: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
