<?php

namespace App\Exports\Example;

use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class MovementInsertExample
{
    public function download($fileName)
    {
        $exampleData = [
            [
                'TIPO' => 'Ex: entrada ou saída',
                'CATEGORIA' => 'Ex: Campanha',
                'VALOR' => 'Ex: 5000.00',
                'CONTA' => 'Ex: Nubank',
                'NÚMERO DA CONTA' => 'Ex: 1234567',
                'NÚMERO DA AGÊNCIA' => 'Ex: 8',
                'DATA DE MOVIMENTAÇÃO' => Carbon::now('America/Sao_Paulo')->format('d/m/Y'),
                'DESCRIÇÃO' => 'Ex: Alguma descrição',
            ],
        ];

        return (new FastExcel(collect($exampleData)))->download($fileName);
    }
}
