<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CustomUnauthorizedResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // Pass the request to the next middleware/controller
            return $next($request);
        } catch (AccessDeniedHttpException $e) {
            // Log the exception for debugging
            Log::error('AccessDeniedHttpException caught in middleware: ' . $e->getMessage());

            // Return a custom JSON response
            return response()->json([
                'statut' => 403,
                'message' => "Vous n'êtes pas autorisé à effectuer cette action.",
                'data' => null
            ], 403);
        }
    }
}
