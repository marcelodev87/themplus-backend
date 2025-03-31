<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            $token = $user->currentAccessToken();

            if ($token && $token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
                $token->delete();

                return response()->json([
                    'message' => 'Token expirado',
                ], 401);
            }
        }

        return $next($request);
    }
}
