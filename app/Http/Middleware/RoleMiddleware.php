<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Asegúrate de que el usuario está autenticado
        if (!$request->user()){
            return response()->json(['message' => 'No estás autenticado.'], 401);
        } 

        if(!$request->user()->hasRole($role)) {
            return response()->json(['message' => 'No tienes permiso para acceder a esta ruta.'], 403);
        }

        return $next($request);
    }
}
