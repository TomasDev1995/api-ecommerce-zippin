<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EnsureTokenMatchesUser
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
        // Obtener el token del encabezado
        $token = $request->bearerToken();

        // Verificar si el token está presente
        if (!$token) {
            return response()->json(['message' => 'Token not provided.'], 401);
        }

        // Obtener el usuario actual autenticado
        $user = Auth::guard('api')->user();

        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Obtener el token del usuario
        $userToken = $user->tokens()->where('token', $token)->first();

        // Verificar si el token coincide con el del usuario
        if (!$userToken) {
            return response()->json(['message' => 'Token does not match.'], 401);
        }

        return $next($request);
    }
}
