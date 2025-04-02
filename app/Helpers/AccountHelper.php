<?php

namespace App\Helpers;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Illuminate\Support\Facades\DB;

class AccountHelper
{
    public static function existsAccount($name, $enterpriseId, $agencyNumber, $accountNumber, $mode, $id)
    {
        $accountRepository = new AccountRepository(new Account);

        if ($mode === 'create') {
            $account = $accountRepository->findByParams($name, $enterpriseId, $agencyNumber, $accountNumber);
            if ($account) {
                throw new \Exception('Já existe uma conta igual ou parecida');
            }
        } else {
            $account = $accountRepository->findByParams($name, $enterpriseId, $agencyNumber, $accountNumber);
            if ($account && $account->id !== $id) {
                throw new \Exception('Já existe uma conta igual ou parecida');
            }
        }
    }

    public static function openingBalance($accountId)
    {
        $account = DB::table('accounts')->where('id', $accountId)->first();

        $category = DB::table('categories')
            ->where('enterprise_id', $account->enterprise_id)
            ->where('name', 'Saldo inicial')
            ->first();

        $result = DB::table('movements')
            ->where('account_id', $account->id)
            ->where('category_id', $category->id)
            ->count();

        if ($result > 0) {
            throw new \Exception('Não é permitido lançar mais de uma vez a categoria "Saldo inicial" para a mesma conta', 403);
        }
    }
}
