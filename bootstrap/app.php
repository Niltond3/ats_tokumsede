<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: x-csrf-token");
header("Access-Control-Allow-Headers: x-xsrf-token");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Accept");
header("Access-Control-Allow-Headers: Acess-Control-Allow-Headers, Acess-Control-Allow-Methods");
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Authorization,charset,boundary,Content-Length');
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: PUT, POST, GET, DELETE, OPTIONS");

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // loads the routes here.
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
            'auth' => Authenticate::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // $middleware->redirectGuestsTo('/cliente/login');
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
