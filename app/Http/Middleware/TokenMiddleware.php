<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Token;

class TokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $tokenId = (new TokenRepository())->find($token);

        if (!$tokenId || !$tokenId->user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->merge(['user_id' => $tokenId->user_id]);

        return $next($request);
    }
}
