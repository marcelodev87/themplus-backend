<?php

namespace App\Http\Controllers;

use App\Helpers\EnterpriseHelper;
use App\Repositories\RegisterRepository;
use App\Rules\RegisterRule;
use Carbon\Carbon;
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
                'register' => $this->treatTextRegister($register),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todas os registros: '.$e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $enterpriseId = $request->user()->view_enterprise_id;
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
                if ($register->action === 'deleted') {
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
                $account = $parts[2];
                $category = $parts[3];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[4])->format('d-m-Y');

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma nova movimentação que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na conta {$account} e data definida como {$date_movement}. Momento de registro: {$register->date_register}";

            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $account = $parts[2];
                $category = $parts[3];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[4])->format('d-m-Y');

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou uma movimentação que continha o valor de R$ {$value} com categoria {$category} do tipo {$type} na conta {$account} e data definida como {$date_movement}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $account = $parts[2];
                $category = $parts[3];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[4])->format('d-m-Y');

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma movimentação que continha o valor de R$ {$value} com categoria {$category} do tipo {$type} na conta {$account} e data definida como {$date_movement}. Momento de registro: {$register->date_register}";
            }
        }
        if ($register->target === 'account') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma conta com o nome de {$name} com número de conta {$account_number} e agência {$agency_number}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou a conta que contém o nome de {$name} com número de conta {$account_number} e agência {$agency_number}. Momento de registro: {$register->date_register}";
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

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} realizou uma transferência da Conta: {$outName} / Número conta: {$outAccountNumber} / Agência: {$outAgencyNumber} para a Conta {$entryName} / Número conta: {$entryAccountNumber} / Agência: {$entryAgencyNumber} no valor R$ {$value}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'reactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reativou uma conta que contém o nome de {$name} com número de conta {$account_number} e agência {$agency_number}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'inactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} inativou uma conta que contém o nome de {$name} com número de conta {$account_number} e agência {$agency_number}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $account_number = $parts[1] === '' ? 'Não definido' : $parts[1];
                $agency_number = $parts[2] === '' ? 'Não definido' : $parts[2];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma conta que contém o nome de {$name} com número de conta {$account_number} e agência {$agency_number}. Momento de registro: {$register->date_register}";
            }
        }
        if ($register->target === 'enterprise') {
            if ($register->action === 'updated') {
                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou os dados da organização {$register->identification}. Momento de registro: {$register->date_register}";
            }
        }
        if ($register->target === 'category') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou uma categoria com o nome de {$name} do tipo {$type}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou uma categoria com o nome de {$name} do tipo {$type}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'reactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reativou uma categoria com o nome de {$name} do tipo {$type}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'inactivated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} inativou uma categoria com o nome de {$name} do tipo {$type}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $type = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu uma categoria com o nome de {$name} cdo tipo {$type}. Momento de registro: {$register->date_register}";
            }
        }
        if ($register->target === 'member') {
            if ($register->action === 'created') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} adicionou um usuário {$name} de e-mail {$email}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou um usuário {$name} de e-mail {$email}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $name = $parts[0];
                $email = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um usuário {$name} de e-mail {$email}. Momento de registro: {$register->date_register}";
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
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d-m-Y');

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} criou um novo agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'updated') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3];
                $accountAgency = $parts[4];
                $category = $parts[5];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d-m-Y');

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} atualizou um agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'finalize') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3];
                $accountAgency = $parts[4];
                $category = $parts[5];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d-m-Y');

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $value = number_format($parts[0], 2, ',', '.');
                $type = $parts[1];
                $accountName = $parts[2];
                $accountNumber = $parts[3];
                $accountAgency = $parts[4];
                $category = $parts[5];
                $date_movement = Carbon::createFromFormat('Y-m-d', $parts[6])->format('d-m-Y');

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} excluiu um agendamento que contém o valor de R$ {$value} com categoria {$category} do tipo {$type} na Conta: {$accountName} / Número conta: {$accountNumber} / Agência: {$accountAgency} e data definida como {$date_movement}. Momento de registro: {$register->date_register}";
            }
        }
        if ($register->target === 'report') {
            if ($register->action === 'delivered') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} finalizou um encerramento do período {$monthYear}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'finalized') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];
                $enterpriseName = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} finalizou um encerramento do período {$monthYear} do cliente {$enterpriseName}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'undone') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];
                $enterpriseName = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reverteu o relatório do período {$monthYear} para não verificado do cliente {$enterpriseName}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'deleted') {
                $parts = explode('|', $register->identification);
                $monthYear = $parts[0];
                $enterpriseName = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} reabriu relatório de movimentações do período {$monthYear} do cliente {$enterpriseName}. Momento de registro: {$register->date_register}";
            }
        }
        if ($register->target === 'order') {
            if ($register->action === 'invite') {
                $parts = explode('|', $register->identification);
                $actionBond = $parts[0] === 'accepted' ? 'aceitou' : 'rejeitou';
                $counter = $parts[1];

                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} {$actionBond} a solicitação da organização de contabilidade nomeada de  {$counter}. Momento de registro: {$register->date_register}";
            }
            if ($register->action === 'unlink') {
                $text = "O(A) usuário(a) {$register->user->name} de e-mail {$register->user->email} desvinculou-se da organização de contabilidade nomeada de{$register->identification}. Momento de registro: {$register->date_register}";
            }
        }

        return $text;
    }
}
