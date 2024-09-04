<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransformResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Exécuter la requête et obtenir la réponse
        $response = $next($request);

        // Si la réponse est déjà une instance de JsonResponse, on la retourne telle quelle
        if ($response instanceof JsonResponse) {
            return $response;
        }

        // Si la réponse est un tableau ou un objet, on la transforme en JSON
        if (is_array($response) || is_object($response)) {
            return response()->json($response);
        }

        // Retourner la réponse inchangée si ce n'est pas un tableau ou un objet
        return $response;
    }
}
