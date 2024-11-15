<?php

namespace App\Exports;

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
                'ID' => $movement->id,
                'TIPO' => $movement->type,
                'VALOR' => $movement->value,
                'DATA DE MOVIMENTAÇÃO' => $movement->date_movement,
                'DESCRIÇÃO' => $movement->description,
                'CATEGORIA' => $movement->category->name ?? '',
                'CONTA' => $movement->account->name ?? '',
                'NÚMERO CONTA' => $movement->account->account_number ?? '',
                'AGÊNCIA' => $movement->account->agency_number ?? '',
                'CRIADO EM' => $movement->created_at->format('Y-m-d H:i:s'),
                'ATUALIZADO EM' => $movement->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
