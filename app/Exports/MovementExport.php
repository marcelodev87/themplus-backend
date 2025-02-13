<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;

class MovementExport
{
    protected $movements;

    public function __construct(Collection $movements)
    {
        $this->movements = $movements;
    }

    public function download($fileName)
    {
        return (new FastExcel($this->movements))->download($fileName, function ($movement) {
            return [
                'TIPO' => $movement->type,
                'VALOR' => 'R$ '.number_format($movement->value, 2, ',', '.'),
                'DATA DE MOVIMENTAÇÃO' => Carbon::parse($movement->date_movement)->format('d/m/Y'),
                'DESCRIÇÃO' => $movement->description,
                'CATEGORIA' => $movement->category->name ?? '',
                'CONTA' => $movement->account->name ?? '',
                'NÚMERO CONTA' => $movement->account->account_number ?? '',
                'AGÊNCIA' => $movement->account->agency_number ?? '',
                'CRIADO EM' => Carbon::parse($movement->created_at)->format('d/m/Y H:i:s'),
                'ATUALIZADO EM' => Carbon::parse($movement->updated_at)->format('d/m/Y H:i:s'),
            ];
        });
    }
}
