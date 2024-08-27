<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);
    
        return $next($request);
    }
    

    /**
     * Authenticate the request for the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     */
    protected function authenticate($request, array $guards)
    {
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return;
            }
        }
    
        throw new AuthenticationException('Unauthenticated.', $guards);
    }
    
}

