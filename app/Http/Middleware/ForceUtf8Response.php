<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class ForceUtf8Response
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->header('Content-Type', 'text/html; charset=utf-8');
        return $response;
    }
}
