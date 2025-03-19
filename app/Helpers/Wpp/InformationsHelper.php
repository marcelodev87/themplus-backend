<?php

namespace App\Helpers\Wpp;

use Illuminate\Support\Facades\DB;


class InformationsHelper
{
    public static function getAccountsAndCategories($phone)
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
            $user = DB::table('users')->where('phone', $phone)->first();
            $categories = DB::table('categories')->select('id', 'name', 'type')
                ->where('enterprise_id', $user->enterprise_id)
                ->where('active', 1)
                ->get();
            $accounts = DB::table('accounts')->select('id', 'name', 'agency_number', 'account_number')
                ->where('enterprise_id', $user->enterprise_id)
                ->where('active', 1)
                ->get();

            return ['categories' => $categories, 'accounts' => $accounts];
        }
    }
}
