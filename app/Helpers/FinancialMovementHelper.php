<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class FinancialMovementHelper
{
    public static function reportFinalized($enterpriseId, $dateMovement)
    {
        $month = date('m', strtotime($dateMovement));
        $year = date('Y', strtotime($dateMovement));

        $result = DB::table('financial_movements')
            ->where('enterprise_id', $enterpriseId)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        if (count($result) > 0) {
            throw new \Exception('Não pode finalizar esse agendamento no mês atual, pois o relatório do mesmo já foi entregue');
        }
    }
}
