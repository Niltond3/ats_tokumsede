<?php

namespace App\Http\Controllers\Cliente\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\Debugbar\Facades\Debugbar;

class AuthenticatedSessionController extends Controller
{

    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Cliente/Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(ClientLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('cliente.dashboard'))
        ->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('Origin')
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('cliente')->logout();

        // $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/cliente/login');
    }
}
