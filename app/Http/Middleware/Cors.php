<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request)->header('Access-Control-Allow-Origin', '*')
        ->header("Access-Control-Allow-Headers","x-csrf-token")
        ->header('Access-Control-Allow-Methods','GET,POST,PUT,PATCH,DELETE,OPTIONS')
        ->header('Access-Control-Allow-Headers','Content-Type,Authorization')
        ->header("Access-Control-Allow-Headers: Origin", "X-Requested-With, Content-Type, Accept")
        ->header("Content-Type", "application/json")
        ->header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers","Content-Type, Acess-Control-Allow-Methods, Authorization");
    }
}
