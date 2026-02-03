<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckIsNotSubscriptionFree
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user) {

            $subscriptionName = DB::table('enterprises')
                ->join('subscriptions', 'subscriptions.id', '=', 'enterprises.subscription_id')
                ->where('enterprises.id', $user->enterprise_id)
                ->value('subscriptions.name');

            if ($subscriptionName === 'free') {
                abort(403, 'Não disponível para plano gratuito');
            }

        } else {
            abort(403, 'Não autenticado');
        }
    }
}
