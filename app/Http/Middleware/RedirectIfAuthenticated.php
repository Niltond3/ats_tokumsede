<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\Debugbar\Facades\Debugbar;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            #check if guards is authenticated
            if (Auth::guard($guard)->check()) {
                Debugbar::info($guard);
                if($guard === 'cliente' && Route::is('cliente.*')) {
                    return redirect()->route('cliente.dashboard');
                }else{
                    return redirect()->route( 'dashboard');
                }
            }
        }

        return $next($request);
    }

}
