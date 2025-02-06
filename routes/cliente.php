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
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EnderecoClienteController;
use Inertia\Inertia;

Route::prefix('cliente')->name('cliente.')->group(function () {
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

        //ENDERECOS CLIENTES
        // Route::get('/enderecos', [EnderecoClienteController::class,'show'])->name('enderecos.show');

        Route::resource('clientes', ClienteController::class, ['except' => 'create'])
    ->names([
        'index' => 'clientes.index',
        'show' => 'clientes.show',
        'store' => 'clientes.store',
        'edit' => 'clientes.edit',
        'update' => 'clientes.update',
        'destroy' => 'clientes.destroy'
    ]);
    Route::put('clientes/{id}/password', [ClienteController::class, 'updatePassword'])->name('cliente.password.update');
    Route::delete('/profile', [ClienteController::class, 'destroy'])
    ->name('cliente.profile.destroy');



// Additional custom routes
Route::get('clientes/buscar-latitude-longitude', [ClienteController::class, 'buscarLatitudeLongitude']);

        //ENDERECOS CLIENTES
        Route::resource('enderecos', EnderecoClienteController::class, ['except' => 'create']);


        Route::get('/dashboard', function () {
            return Inertia::render('Cliente/Dashboard');
        })->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
        //PEDIDOS
        Route::group(['prefix' => 'pedidos'], function () {
            Route::resource('/', PedidoController::class, ['except' => 'create'])
                ->names([
                    'index' => 'pedidos.index',
                    'show' => 'pedidos.show',
                    'store' => 'pedidos.store',
                    'edit' => 'pedidos.edit',
                    'update' => 'pedidos.update',
                    'destroy' => 'pedidos.destroy'
                ]);
            Route::get('visualizar/{id}', [PedidoController::class, 'visualizar']);
            Route::put('aceitar/{id}', [PedidoController::class, 'aceitar']);
            Route::put('despachar/{id}', [PedidoController::class, 'despachar']);
            Route::put('recusar/{id}', [PedidoController::class, 'recusar']);
            Route::put('entregar/{id}', [PedidoController::class, 'entregar']);
            Route::put('cancelar/{id}', [PedidoController::class, 'cancelar']);
            Route::get('editar/{id}', [PedidoController::class, 'editar']);
            Route::put('atualizar/{id}', [PedidoController::class, 'atualizar']);
            Route::get('escolherentregador/{id}', [PedidoController::class, 'escolherentregador']);
            Route::get('ajustarCoordenadas/{id}', [PedidoController::class, 'ajustarCoordenadas']);
            Route::post('ajustarCoordenadas/{id}', [PedidoController::class, 'ajustarCoordenadas']);
            Route::get('buscarNovosPedidos/{id}', [PedidoController::class, 'buscarNovosPedidos']);
            Route::get('ultimoPedido', [PedidoController::class, 'ultimoPedido']);
            Route::get('listaClientes', [PedidoController::class, 'listaClientes']);
        });
        //PRODUTOS
        Route::group(['prefix' => 'produtos'], function () {
            Route::resource('/', ProdutoController::class, ['except' => 'create'])
                ->names([
                    'index' => 'produtos.index',
                    'show' => 'produtos.show',
                    'store' => 'produtos.store',
                    'edit' => 'produtos.edit',
                    'update' => 'produtos.update',
                    'destroy' => 'produtos.destroy'
                ]);
            Route::get('listarProdutos/{idDistribuidor}/{idCliente}', [ProdutoController::class, 'listarProdutos']);
            Route::get('{idEnderecoCliente}', [ProdutoController::class, 'show']);
        });
    });

});
