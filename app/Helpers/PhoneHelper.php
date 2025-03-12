<?php

namespace App\Helpers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PhoneHelper
{
    public static function validPhone($phone)
    {
        $result = DB::table('users')->where('phone', $phone)->count();
        if ($result === 0) {
            throw new \Exception(
                'O número de telefone não pertence a nenhum usuário.',
                404
            );
        } else if ($result > 1) {
            throw new \Exception(
                'O número de telefone está registrado em mais de um usuário, entre em contato com o administrador',
                403
            );
        } else {
            return true;
        }
    }
}
