<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AsaasWebhookMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('asaas-access-token');

        if (! $token || $token !== config('app.asaas_webhook_token')) {
            return response()->json(['message' => 'Token inválido'], 403);
        }

        return $next($request);
    }
}
