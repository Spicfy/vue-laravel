<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param callable|string|null $guests
     * @param callable|string|null $users
     * @return string|null
     */
    public function redirectTo(\Illuminate\Http\Request $request)
    {
        if (!request()->expectsJson()) {
            return route('login');
        }

        return null; // Return null if no redirection is required
    }
    public function handle($request, Closure $next, ...$guards)
    {

        if($jwt = $request->cookie('jwt')){
            $request->headers->set('Authorization', 'Bearer ' . $jwt);
        }
        $this->authenticate($request, $guards);

        return $next($request);
    }
}
