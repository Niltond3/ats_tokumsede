<?php

include 'cors.php';
use App\Http\Controllers\Cliente\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest:cliente')->group(function () {

    Route::get('cliente/login', [AuthenticatedSessionController::class, 'create'])
                ->name('cliente.login');

    Route::post('cliente/login', [AuthenticatedSessionController::class, 'store']);

});

Route::middleware('auth:cliente')->group(function () {
    Route::post('cliente/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('cliente.logout');
});
