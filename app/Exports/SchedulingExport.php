<?php

namespace App\Exports;

use Carbon\Carbon;
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
                'TIPO' => $scheduling->type,
                'VALOR' => 'R$ '.number_format($scheduling->value, 2, ',', '.'),
                'DATA DE AGENDAMENTO' => Carbon::parse($scheduling->date_movement)->format('d/m/Y'),
                'DESCRIÇÃO' => $scheduling->description,
                'CATEGORIA' => $scheduling->category->name ?? '',
                'CONTA' => $scheduling->account->name ?? '',
                'NÚMERO CONTA' => $scheduling->account->account_number ?? '',
                'AGÊNCIA' => $scheduling->account->agency_number ?? '',
                'CRIADO EM' => Carbon::parse($scheduling->created_at)->format('d/m/Y H:i:s'),
                'ATUALIZADO EM' => Carbon::parse($scheduling->updated_at)->format('d/m/Y H:i:s'),
            ];
        });
    }
}
