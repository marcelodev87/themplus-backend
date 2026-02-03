<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovementHelper
{
    public static function allowCreateMovement(string $enterpriseId, $period): void
    {
        $year = $period->year;
        $month = $period->month;

        $subscriptionName = DB::table('enterprises')
            ->join('subscriptions', 'subscriptions.id', '=', 'enterprises.subscription_id')
            ->where('enterprises.id', $enterpriseId)
            ->value('subscriptions.name');

        if (! $subscriptionName) {
            throw ValidationException::withMessages([
                'enterprise' => ['Subscription não encontrada'],
            ]);
        }

        if ($subscriptionName === 'free') {

            $countMovements = DB::table('movements')
                ->where('enterprise_id', $enterpriseId)
                ->whereYear('date_movement', $year)
                ->whereMonth('date_movement', $month)
                ->count();

            if ($countMovements >= 50) {
                throw ValidationException::withMessages([
                    'limit' => [
                        'Plano free permite no máximo 50 movimentações por mês',
                    ],
                ]);
            }
        }
    }
}
