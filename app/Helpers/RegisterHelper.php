<?php

namespace App\Helpers;

use App\Models\Register;
use App\Repositories\RegisterRepository;
use App\Rules\RegisterRule;
use Carbon\Carbon;

class RegisterHelper
{
    public static function create($userId, $enterpriseId, $action, $target, $identification)
    {
        $registerRepository = new RegisterRepository(new Register);

        $data = [
            'user_id' => $userId,
            'enterprise_id' => $enterpriseId,
            'action' => $action,
            'target' => $target,
            'identification' => $identification,
            'date_register' => Carbon::now('America/Sao_Paulo')->format('d-m-Y H:i:s'),
        ];

        $registerRule = new RegisterRule;
        $registerRule->create($data);

        $register = $registerRepository->create($data);
        if (! $register) {
            throw new \Exception('Erro ao registrar processo nos registros');
        } else {
            return $register;
        }
    }
}
