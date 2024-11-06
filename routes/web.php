<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::fallback(function () {
    return view('app');
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/home', function () {
    return Inertia::render('Home');
})->middleware(['auth', 'verified'])->name('home');


// Route::get('/dashboard', function () {
//     return view('home-one');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clientes', ClienteController::class,['except' => 'create']);

    //PEDIDOS
Route::group(['prefix' => 'pedidos'], function(){
    Route::resource('/', PedidoController::class,['except' => 'create']);
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
});

//PRODUTOS
Route::group(['prefix' => 'produtos'], function(){
    Route::resource('/', ProdutoController::class,['except' => 'create']);
    Route::get('listarProdutos/{idDistribuidor}/{idCliente}', [ProdutoController::class,'listarProdutos']);
    Route::get('{idEnderecoCliente}', [ProdutoController::class,'show']);
});

Route::get('/homepage', [HomeController::class, 'getHomepage'])->name('homepage');




require __DIR__.'/auth.php';
require __DIR__.'/cliente.php';
