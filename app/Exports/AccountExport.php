<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;

class AccountExport
{
    protected $accounts;

    public function __construct(Collection $accounts)
    {
        $this->accounts = $accounts;
    }

    public function download($fileName)
    {
        return (new FastExcel($this->accounts))->download($fileName, function ($account) {
            return [
                'ID' => $account->id,
                'NOME' => $account->name,
                'NÚMERO CONTA' => $account->account_number,
                'AGÊNCIA' => $account->agency_number,
                'SALDO' => $account->balance,
                'DESCRIÇÃO' => $account->description,
                'CRIADO EM' => $account->created_at->format('Y-m-d H:i:s'),
                'ATUALIZADO EM' => $account->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
