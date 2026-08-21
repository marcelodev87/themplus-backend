<?php

namespace App\Http\Middleware;

use App\Services\CounterLicenseService;
use Closure;
use Illuminate\Http\Request;

class CheckCounterLicenseCompliance
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Não autenticado');
        }

        try {
            app(CounterLicenseService::class)->checkCompliance($user->enterprise_id);
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        return $next($request);
    }
}
