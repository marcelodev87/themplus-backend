<?php

namespace App\Http\Controllers;

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

            return response()->json(['movements' => $movements], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas as pré-movimentações: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->rule->store($request);
            $enterpriseId = $this->verifyUserByPhone($request->input('phone'));

            $data = [
                'date_movement' => $request->input('date_movement'),
                'value' => $request->input('value'),
                'type' => $request->input('type'),
                'description' => $request->input('description'),
                'enterprise_id' => $enterpriseId
            ];
            $movement = $this->service->create($data);

            if ($movement) {
                DB::commit();

                return response()->json(201);
            }

            throw new \Exception('Falha ao criar pré-movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao registrar pré-movimentação: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function verifyUserByPhone($phone)
    {
        $users = $this->userRepository->getUsersByPhone($phone);

        if (empty($users)) {
            throw new \Exception(
                'Nenhum usuário no sistema está registrado com esse número de telefone',
                404
            );
        }

        if (count($users) > 1) {
            throw new \Exception(
                'O mesmo número de telefone está registrado em mais de um usuário, entre em contato com o administrador',
                403
            );
        }

        $user = current($users);
        return $user->enterprise_id;
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
            $movement = $this->service->create($request);

            if ($movement) {
                DB::commit();
                return response()->json([
                    'message' => 'Pré-Movimentação validada com sucesso',
                ], 201);
            }

            throw new \Exception('Falha ao validar pré-movimentação');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao validar pré-movimentação : ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function calculateValueAccount($movements)
    {
        $value = 0;

        foreach ($movements as $movement) {
            if ($movement->type === 'entrada') {
                $value += $movement->value;
            } elseif ($movement->type === 'saída') {
                $value -= $movement->value;
            } else {
                $category = $this->categoryRepository->findById($movement->category_id);
                if ($category->type === 'entrada') {
                    $value += $movement->value;
                } else {
                    $value -= $movement->value;
                }
            }
        }

        return $value;
    }

    public function updateBalanceAccount($accountId)
    {
        $movements = $this->movementRepository->getAllByAccount($accountId);
        $newValueAccount = $this->calculateValueAccount($movements);

        return $this->accountRepository->updateBalance($accountId, $newValueAccount);
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
