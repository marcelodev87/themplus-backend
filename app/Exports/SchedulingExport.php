<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;

class SchedulingExport
{
    protected $schedulings;

    public function __construct(Collection $schedulings)
    {
        $this->schedulings = $schedulings;
    }

    public function download($fileName)
    {
        return (new FastExcel($this->schedulings))->download($fileName, function ($scheduling) {
            return [
                'ID' => $scheduling->id,
                'TIPO' => $scheduling->type,
                'VALOR' => $scheduling->value,
                'DATA DE MOVIMENTAÇÃO' => $scheduling->date_movement,
                'DESCRIÇÃO' => $scheduling->description,
                'CATEGORIA' => $scheduling->category->name ?? '',
                'CONTA' => $scheduling->account->name ?? '',
                'NÚMERO CONTA' => $scheduling->account->account_number ?? '',
                'AGÊNCIA' => $scheduling->account->agency_number ?? '',
                'CRIADO EM' => $scheduling->created_at->format('Y-m-d H:i:s'),
                'ATUALIZADO EM' => $scheduling->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
