<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Helpers\NotificationsHelper;
use App\Repositories\RegisterRepository;
use App\Repositories\UserRepository;
use App\Rules\RegisterRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController
{
    private $repository;

    private $userRepository;

    private $rule;

    public function __construct(RegisterRepository $repository, RegisterRule $rule, UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->rule = $rule;
        $this->userRepository = $userRepository;
    }

    public function show($id)
    {
        try {
            $this->rule->show($id);
            $register = $this->repository->findById($id);

            return response()->json([
                'register' => $this->treatTextRegister($register),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os registros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request, $period)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
            $registers = $this->repository->getAllByEnterprise($enterpriseId, $period);
            $filledData = EnterpriseHelper::filledData($enterpriseId);
            $notifications = NotificationsHelper::getNoRead($request->user()->id);

            return response()->json([
                'registers' => $this->treatRegister($registers),
                'notifications' => $notifications,
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
                if ($register->action === 'insert') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} criou uma nova movimentação a partir de uma inserção de planilha",
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
            if ($register->target === 'office') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} criou uma nova filial",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} deletou uma filial",
                    ];
                }
            }
            if ($register->target === 'permission') {
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou as permissões de contabilidade",
                    ];
                }
            }
            if ($register->target === 'department') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} criou um novo departamento",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou um departamento",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} deletou um departamento",
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
                        'text' => "O(A) usuário(a) {$register->user->name} criou uma categoria",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou uma categoria",
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
                if ($register->action === 'inactivated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} inativou um usuário",
                    ];
                }
                if ($register->action === 'reactivated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} reativou um usuário",
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
                if ($register->action === 'finalized') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} finalizou a entrega de um relatório",
                    ];
                }
                if ($register->action === 'undone') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} reverteu relatório para não verificado",
                    ];
                }
                if ($register->action === 'reopen') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} reabriu relatório de movimentações.",
                    ];
                }
            }
            if ($register->target === 'order') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} enviou uma solicitação de vínculo com uma organização",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou uma descrição de solicitação de vínculo com uma organização",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu uma solicitação de vínculo com uma organização",
                    ];
                }
                if ($register->action === 'invite') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} aceitou/rejeitou uma solicitação de vínculo com organização de contabilidade",
                    ];
                }
                if ($register->action === 'unlink') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} desvinculou a própria organização de uma organização de contabilidade.",
                    ];
                }
            }
            if ($register->target === 'bond') {
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu um vínculo com uma organização",
                    ];
                }
            }
            if ($register->target === 'alert') {
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou alertas de categoria de uma organização",
                    ];
                }
            }
            if ($register->target === 'manageUser') {
                if ($register->action === 'created') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} adicionou um usuário em uma organização",
                    ];
                }
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou um usuário de uma organização",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu um usuário de uma organização",
                    ];
                }
            }
            if ($register->target === 'manageMovement') {
                if ($register->action === 'updated') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou uma movimentação de uma organização",
                    ];
                }
                if ($register->action === 'observations') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} atualizou observações de movimentações de uma organização",
                    ];
                }
                if ($register->action === 'deleted') {
                    $dataProcessed[] = [
                        'id' => $register->id,
                        'user_name' => $register->user->name,
                        'user_email' => $register->user->email,
                        'date' => $register->date_register,
                        'action' => $register->action,
                        'text' => "O(A) usuário(a) {$register->user->name} excluiu uma movimentação de uma organização",
                    ];
                }
            }
        }

        return $dataProcessed;
    }

    public function treatTextRegister($register)
    {
        $text = '';
        if ($register->target === 'movement') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3] === '' ? 'Não definido' : $parts[3];
                $accountAgency = $parts[4] === '' ? 'Não definido' : $parts[4];
                $category = $parts[5];
                $dateMovement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma nova movimentação que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$dateMovement}. Momento de registro: {$dateRegisterFormatted}";

            }

            if ($register->action === 'insert') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3 === '' ? 'Não definido' : $parts[3]];
                $accountAgency = $parts[4] === '' ? 'Não definido' : $parts[4];
                $category = $parts[5];
                $dateMovement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma nova movimentação a partir de uma inserção de planilha, que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$dateMovement}. Momento de registro: {$dateRegisterFormatted}";

            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3] === '' ? 'Não definido' : $parts[3];
                $accountAgency = $parts[4] === '' ? 'Não definido' : $parts[4];
                $category = $parts[5];
                $dateMovement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou uma movimentação que continha o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$dateMovement}. Momento de registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3] === '' ? 'Não definido' : $parts[3];
                $accountAgency = $parts[4] === '' ? 'Não definido' : $parts[4];
                $category = $parts[5];
                $dateMovement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma movimentação que continha o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$dateMovement}. Momento de registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'account') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma conta: Nome: {$name} / Número: {$account_number} / Agência: {$agency_number}. Momento de registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou a conta: Nome: {$name} / Número: {$account_number} /  Agência: {$agency_number}. Momento de registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'transfer') {
                $parts = explode('|', $register->identification);
                $outName = $parts[0];
                $outAccountNumber = $parts[1] === '' ? 'Não definido' : $parts[1];
                $outAgencyNumber = $parts[2] === '' ? 'Não definido' : $parts[2];
                $entryName = $parts[3];
                $entryAccountNumber = $parts[4] === '' ? 'Não definido' : $parts[4];
                $entryAgencyNumber = $parts[5] === '' ? 'Não definido' : $parts[5];
                $value = number_format($parts[6], 2, ',', '.');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} realizou uma transferência da Conta: {$outName} / Número conta: {$outAccountNumber} / Agência: {$outAgencyNumber} para a Conta {$entryName} / Número conta: {$entryAccountNumber} / Agência: {$entryAgencyNumber} no valor R$ {$value}. Momento de registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'reactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reativou uma conta: Nome: {$name} / Número: {$account_number} / Agência: {$agency_number}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'inactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} inativou uma conta: Nome {$name} / Número: {$account_number} /  Agência {$agency_number}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma conta: Nome {$name} / Número: {$account_number} /  Agência {$agency_number}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'enterprise') {
            if ($register->action === 'updated') {
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou os dados da organização {$register->identification}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'permission') {
            if ($register->action === 'updated') {
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou as permissões de contabilidade. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'office') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma nova filial: {$name}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma filial: {$name}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'department') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou um novo departamento: {$name}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou um departamento: {$name}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um departamento: {$name}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'category') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma categoria: Nome: {$name} / Tipo: {$type}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou uma categoria: Nome: {$name} / Tipo: {$type}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'reactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reativou uma categoria: Nome: {$name} / Tipo: {$type}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'inactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} inativou uma categoria: Nome: {$name} / Tipo: {$type}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma categoria: Nome: {$name} / Tipo: {$type}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'member') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} adicionou um usuário: Nome: {$name} / E-mail: {$email}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou um usuário: Nome: {$name} / E-mail: {$email}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'inactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} inativou um usuário: Nome: {$name} / E-mail: {$email}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'reactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reativou um usuário: Nome: {$name} / E-mail: {$email}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um usuário: Nome: {$name} / E-mail: {$email}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'scheduling') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3];
                $accountAgency = $parts[4];
                $category = $parts[5];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou um novo agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3];
                $accountAgency = $parts[4];
                $category = $parts[5];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou um agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'finalize') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3];
                $accountAgency = $parts[4];
                $category = $parts[5];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} finalizou um agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3];
                $accountAgency = $parts[4];
                $category = $parts[5];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d/m/Y');
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'report') {
            if ($register->action === 'delivered') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} entregou relatório de movimentações do período {$monthYear}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'finalized') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];
                $enterpriseName = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} finalizou um encerramento do período {$monthYear} do cliente {$enterpriseName}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'undone') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];
                $enterpriseName = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reverteu o relatório do período {$monthYear} para não verificado do cliente {$enterpriseName}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'reopen') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];
                $enterpriseName = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reabriu relatório de movimentações do período {$monthYear} do cliente {$enterpriseName}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'order') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $enterpriseName = $parts[0];
                $enterpriseEmail = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} enviou uma solicitação de vínculo para organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $enterpriseName = $parts[0];
                $enterpriseEmail = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou uma descrição de vínculo para organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $enterpriseName = $parts[0];
                $enterpriseEmail = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma solicitação de vínculo para organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'invite') {
                $parts = explode('|', $register->identification);
                $actionBond = $parts[0] === 'accepted' ? 'aceitou' : 'rejeitou';
                $counter = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} {$actionBond} a solicitação da organização de contabilidade nomeada de {$counter}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'unlink') {
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} desvinculou-se da organização de contabilidade nomeada de {$register->identification}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'bond') {
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $enterpriseName = $parts[0];
                $enterpriseEmail = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um de vínculo com a organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'manageUser') {
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $userName = $parts[0];
                $userEmail = $parts[1];
                $enterpriseName = $parts[2];
                $enterpriseEmail = $parts[3];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um usuário: Nome {$userName} / E-mail: {$userEmail} da organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $userName = $parts[0];
                $userEmail = $parts[1];
                $enterpriseName = $parts[2];
                $enterpriseEmail = $parts[3];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} adicionou um usuário: Nome {$userName} / E-mail: {$userEmail} na organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $userName = $parts[0];
                $userEmail = $parts[1];
                $enterpriseName = $parts[2];
                $enterpriseEmail = $parts[3];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou um usuário: Nome {$userName} / E-mail: {$userEmail} da organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'manageMovement') {
            if ($register->action === 'observations') {
                $parts = explode('|', $register->identification);
                $period = $parts[0];
                $enterpriseName = $parts[1];
                $enterpriseEmail = $parts[2];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou observações para movimentações do periodo {$period} da organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $dateMovement = Carbon::createFromFormat('Y-m-d', $parts[2])->format('d/m/Y');
                $enterpriseName = $parts[3];
                $enterpriseEmail = $parts[4];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou uma movimentação: Valor: R$ {$value} / Tipo: {$type} / Data de movimentação: {$dateMovement}  da organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $dateMovement = Carbon::createFromFormat('Y-m-d', $parts[2])->format('d/m/Y');
                $enterpriseName = $parts[3];
                $enterpriseEmail = $parts[4];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma movimentação: Valor: R$ {$value} / Tipo: {$type} / Data de movimentação: {$dateMovement}  da organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
        }
        if ($register->target === 'alert') {
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $enterpriseName = $parts[0];
                $enterpriseEmail = $parts[1];
                $dateRegisterFormatted = str_replace('-', '/', $register->date_register);

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou alertas de categorias da organização: Nome: {$enterpriseName} / E-mail: {$enterpriseEmail}. Momento do registro: {$dateRegisterFormatted}";
            }
        }

        return $text;
    }
}
