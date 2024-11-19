<?php

include 'cors.php';
use App\Http\Controllers\Cliente\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Cliente\ProfileController;
use App\Http\Controllers\Cliente\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Cliente\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Cliente\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Cliente\Auth\NewPasswordController;
use App\Http\Controllers\Cliente\Auth\PasswordController;
use App\Http\Controllers\Cliente\Auth\PasswordResetLinkController;
use App\Http\Controllers\Cliente\Auth\RegisteredUserController;
use App\Http\Controllers\Cliente\Auth\VerifyEmailController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('cliente')->name('cliente.')->group(function (){
    Route::middleware('guest:cliente')->group(function () {
        Route::get('register', [RegisteredUserController::class, 'create'])
                    ->name('register');

        Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
                    ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        // Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        //             ->name('password.request');

        // Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        //             ->name('password.email');

        // Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        //             ->name('password.reset');

        // Route::post('reset-password', [NewPasswordController::class, 'store'])
        //             ->name('password.store');
    });

    Route::middleware('auth:cliente')->group(function () {
        // Route::get('verify-email', EmailVerificationPromptController::class)
        //             ->name('verification.notice');

        // Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        //             ->middleware(['signed', 'throttle:6,1'])
        //             ->name('verification.verify');

        // Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        //             ->middleware('throttle:6,1')
        //             ->name('verification.send');

        // Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        //             ->name('password.confirm');

        // Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::get('/dashboard', function () {
            return Inertia::render('Cliente/Dashboard');
        })->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');

                    //PRODUTOS
    Route::prefix('produtos')->name('produtos.')->group(function(){
    Route::get('listarProdutos/{idDistribuidor}/{idCliente}', [ProdutoController::class, 'listarProdutos']);
    Route::get('{idEnderecoCliente}', [ProdutoController::class, 'show']);
});
    });
});
