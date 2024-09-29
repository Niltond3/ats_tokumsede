<?php

use App\Http\Controllers\Auth\ClientAuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:cliente')->group(function () {

    Route::get('cliente/login', [ClientAuthenticatedSessionController::class, 'create'])
                ->name('cliente.login');

    Route::post('cliente/login', [ClientAuthenticatedSessionController::class, 'store']);

});

Route::middleware('auth:cliente')->group(function () {
    Route::post('cliente/logout', [ClientAuthenticatedSessionController::class, 'destroy'])
                ->name('cliente.logout');
});
