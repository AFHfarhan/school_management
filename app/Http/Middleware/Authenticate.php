<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {

            # if guard is authenticated
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);
                return $next($request);
            }
        }

        # Handle un authenticated session
        $this->unauthenticated($guards);
    }

    /**
     * Function to handle unauthenticated request
     * @param array $guards
     */
    protected function unauthenticated(array $guards) {
        throw new AuthenticationException(
            'Unauthenticated', $guards, $this->redirectTo()
        );
    }

    /**
     * Function to redirect based on route
     */
    protected function redirectTo() {

        # For admin teacher
        if (Route::is('v1.*')) {
            return route('v1.login');
        }

        # For else part - normal user
        return route('v1.login');
    }
}
