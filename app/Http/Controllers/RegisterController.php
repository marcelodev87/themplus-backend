<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\RegisterRepository;
use App\Rules\RegisterRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController
{
    private $repository;

    private $userRepository;

    private $rule;

    public function __construct(RegisterRepository $repository, RegisterRule $rule)
    {
        $this->repository = $repository;
        $this->rule = $rule;
    }

    public function show($id)
    {
        try {
            $this->rule->show($id);
            $register = $this->repository->findById($id);

            return response()->json([
                'register' => $this->treatRegister($register),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os registros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->enterprise_id;
            $registers = $this->repository->getAllByEnterprise($enterpriseId);
            $filledData = EnterpriseHelper::filledData($enterpriseId);

            return response()->json([
                'registers' => $this->treatRegister($registers),
                'filled_data' => $filledData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os registros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function treatRegister($registers)
    {
        $dataProcessed = [];

        foreach ($registers as $register) {
            if ($register->target === 'movement') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} criou uma nova movimentação",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou uma movimentação",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu uma movimentação",
                    ];
                }
            }
            if ($register->target === 'account') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} criou uma nova conta",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou uma conta",
                    ];
                }
                if ($register->action === 'transfer') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} transferiu valores entre contas",
                    ];
                }
                if ($register->action === 'reactivated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} reativou uma conta",
                    ];
                }
                if ($register->action === 'inactivated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} inativou uma conta",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu uma conta",
                    ];
                }
            }
            if ($register->target === 'enterprise') {
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou os dados da organização",
                    ];
                }
            }
            if ($register->target === 'category') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} : {$register->user->email} criou uma categoria",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} : {$register->user->email} atualizou uma categoria",
                    ];
                }
                if ($register->action === 'reactivated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} reativou uma categoria",
                    ];
                }
                if ($register->action === 'inactivated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} inativou uma categoria",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu uma categoria",
                    ];
                }
            }
            if ($register->target === 'member') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} adicionou um novo usuário",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou dados de um usuário",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu um usuário",
                    ];
                }
            }
            if ($register->target === 'scheduling') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} adicionou um novo agendamento",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou um agendamento",
                    ];
                }
                if ($register->action === 'finalize') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} finalizou um agendamento",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu um agendamento",
                    ];
                }
            }
            if ($register->target === 'report') {
                if ($register->action === 'delivered') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} entregou um relatório",
                    ];
                }
            }
        }

        return $dataProcessed;

    }
}
