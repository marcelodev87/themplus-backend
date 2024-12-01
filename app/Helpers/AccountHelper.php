<?php

namespace App\Helpers;

use App\Models\Account;
use App\Repositories\AccountRepository;

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
}
