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
                'DATA DE MOVIMENTAÇÃO' => Carbon::now('America/Sao_Paulo')->format('d/m/Y'),
                'TIPO' => 'Ex: entrada ou saída',
                'VALOR' => 'Ex: 5000.00',
                'DESCRIÇÃO' => 'Ex: Alguma descrição',
            ],
        ];

        return (new FastExcel(collect($exampleData)))->download($fileName);
    }
}
