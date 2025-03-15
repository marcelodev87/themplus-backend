<?php

namespace App\Http\Controllers;

use App\Helpers\PhoneHelper;
use App\Repositories\AccountRepository;
use App\Repositories\MovementAnalyzeRepository;
use App\Repositories\UserRepository;
use App\Repositories\FinancialRepository;
use App\Repositories\MovementRepository;
use App\Repositories\CategoryRepository;
use App\Services\MovementAnalyzeService;
use App\Rules\MovementAnalyzeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovementAnalyzeController
{

    private $userRepository;
    private $financialRepository;
    private $movementRepository;
    private $accountRepository;
    private $categoryRepository;
    private $repository;
    private $service;
    private $rule;

    public function __construct(MovementAnalyzeRepository $repository, MovementAnalyzeRule $rule, UserRepository $userRepository, FinancialRepository $financialRepository, MovementRepository $movementRepository, CategoryRepository $categoryRepository, AccountRepository $accountRepository, MovementAnalyzeService $service)
    {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->accountRepository = $accountRepository;
        $this->financialRepository = $financialRepository;
        $this->movementRepository = $movementRepository;
        $this->service = $service;
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function index(Request $request)
    {
        try {
            $movements = $this->repository->getAllByEnterprise($request->user()->enterprise_id);
            $categories = $this->categoryRepository->getAllByEnterpriseWithDefaultsOnlyActive(
                $request->user()->enterprise_id,
                null
            );
            $accounts = $this->accountRepository->getAllByEnterpriseOnlyActive($request->user()->enterprise_id);

            return response()->json([
                'movements_analyze' => $movements,
                'categories' => $categories,
                'accounts' => $accounts
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as pré-movimentações: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function checkPhone(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',

            ]);
            $result = PhoneHelper::validPhone($request->input('phone'));

            if ($result) {
                return response()->json([
                    'result' => 'Número de telefone registrado',
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao registrar pré-movimentação: ' . $e->getMessage());
            $status = $e->getCode() ? $e->getCode() : 500;
            return response()->json(['message' => $e->getMessage()], $status);
        }
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->rule->store($request);
            $movement = $this->service->create($request);

            if ($movement) {
                DB::commit();
                return response()->json(201);
            }

            throw new \Exception('Falha ao criar pré-movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar pré-movimentação: ' . $e->getMessage());

            $status = $e->getCode() ? $e->getCode() : 500;

            return response()->json(['message' => $e->getMessage()], $status);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->rule->update($request);
            $data = [
                'date_movement' => $request->input('date_movement'),
                'value' => $request->input('value'),
                'type' => $request->input('type'),
                'description' => $request->input('description'),
            ];
            $movement = $this->repository->update($request->input('id'), $data);

            if ($movement) {
                DB::commit();
                $movements = $this->repository->getAllByEnterprise($request->user()->enterprise_id);

                return response()->json(['movements' => $movements, 'nessage' => 'Pré-Movimentação atualizada com sucesso'], 200);
            }

            throw new \Exception('Falha ao atualizar pré-movimentação');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao atualizar pré-movimentação com alerta: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->rule->finalize($request);
            $movement = $this->service->finalize($request);

            if ($movement) {
                DB::commit();

                $movements = $this->repository->getAllByEnterprise($request->user()->enterprise_id);
                $categories = $this->categoryRepository->getAllByEnterpriseWithDefaultsOnlyActive(
                    $request->user()->enterprise_id,
                    null
                );
                $accounts = $this->accountRepository->getAllByEnterpriseOnlyActive($request->user()->enterprise_id);

                return response()->json([
                    'message' => 'Pré-Movimentação validada com sucesso',
                    'movements_analyze' => $movements,
                    'categories' => $categories,
                    'accounts' => $accounts
                ], 201);
            }

            throw new \Exception('Falha ao validar pré-movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao validar pré-movimentação : ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $this->rule->delete($id);
            $movement = $this->repository->delete($id);

            if ($movement) {
                DB::commit();

                return response()->json(['message' => 'Pré-Movimentação excluída com sucesso'], 200);
            }

            throw new \Exception('Falha ao deletar pré-movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao deletar pré-movimentação: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
