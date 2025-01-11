<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\Debugbar\Facades\Debugbar;

class ClientAuthenticatedSessionController extends Controller
{
    protected $redirectTo = '/cliente';
    protected $guard = 'cliente';
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        Debugbar::info('create');
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

     /**
     * Handle an incoming authentication request.
     */
    public function store(ClientLoginRequest $request): RedirectResponse
    {
        Debugbar::info('store');
        Debugbar::info($request);

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard(name: 'cliente')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
