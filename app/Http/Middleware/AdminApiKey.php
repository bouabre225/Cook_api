<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = env('ADMIN_API_KEY');

        // Si ADMIN_API_KEY n'est pas définie en prod, on bloque tout accès
        if (empty($expectedKey)) {
            return response()->json([
                'message' => 'Administration non configurée. Définissez ADMIN_API_KEY dans .env.',
            ], 503);
        }

        $providedKey = $request->header('X-Admin-Key');

        if (empty($providedKey) || !hash_equals($expectedKey, $providedKey)) {
            return response()->json([
                'message' => 'Accès refusé. Clé d\'administration invalide ou manquante.',
            ], 401);
        }

        return $next($request);
    }
}
