<?php
include 'cors.php';

use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DistribuidorController;
use App\Http\Controllers\EntregadorController;
use App\Http\Controllers\EnderecoClienteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\PrecoController;
use App\Http\Controllers\StockUnionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::fallback(function () {
    return Inertia::render('App', [
        'page' => [
            'component' => 'App',
            'props' => [],
            'url' => url()->current(),
        ]
    ]);
});


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/home/{tab?}', function ($tab = null) {
    return Inertia::render('Home', [
        'currentTab' => $tab
    ]);
})->middleware(['auth', 'verified'])->name('home');

Route::get('/home/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/home/register/produto', function () {
    return Inertia::render('Home');
})->middleware(['auth', 'verified'])->name('novo-produto');

Route::get('auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('social.redirect');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('social.callback');
Route::post('/auth/google/callback', [SocialAuthController::class, 'callbackToken']);

// Route::get('/dashboard', function () {
//     return view('home-one');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clientes', ClienteController::class, ['except' => 'create']);

    Route::post('/upload', [FileUploadController::class, 'upload']);

    //ENDERECOS CLIENTES
    Route::resource('enderecos', EnderecoClienteController::class, ['except' => 'create']);
    Route::put('/enderecos/{id}/coordinates', [EnderecoClienteController::class, 'updateCoordinates'])
        ->name('enderecos.coordinates.update');

    //PEDIDOS
    Route::group(['prefix' => 'pedidos'], function () {
        Route::resource('/', PedidoController::class, ['except' => 'create'])
            ->names([
                'index' => 'pedidos.index',
                'store' => 'pedidos.store',
                'show' => 'pedidos.show',
                'update' => 'pedidos.update',
                'destroy' => 'pedidos.destroy',
            ]);
        Route::get('visualizar/{id}', [PedidoController::class, 'visualizar']);
        Route::put('setPendente/{id}', [PedidoController::class, 'setPendente']);
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
                'store' => 'produtos.store',
                'show' => 'produtos.show',
                'edit' => 'produtos.edit',
                'destroy' => 'produtos.destroy'
            ]);
            Route::put('{produto}', [ProdutoController::class, 'update'])->name('produtos.update');
        Route::get('listarProdutos/{idDistribuidor}/{idCliente}', [ProdutoController::class, 'listarProdutos'])->name('produtos.listar');
        Route::get('listarPorDistribuidor/{idDistribuidor}/{idCliente?}', [ProdutoController::class, 'showByDistribuidor'])->name('produtos.por-distribuidor');
        Route::get('{idEnderecoCliente}', [ProdutoController::class, 'show'])->name('produtos.show-by-endereco');
        Route::put('status/{idProduto}/{idStatus}', [ProdutoController::class, 'updateStatus'])->name('produtos.status.update');


    });

    //DISTRIBUIDORES
    Route::group(['prefix' => 'distribuidores'], function () {
        // Rotas customizadas
        Route::get('/all', [DistribuidorController::class, 'getAllDistributors'])
            ->name('distribuidores.todos');

        Route::get('/by-address/{addressId}', [DistribuidorController::class, 'findDistributorByAddress'])
            ->name('distribuidores.por-endereco');

        Route::get('/by-client-address/{clientAddressId}', [DistribuidorController::class, 'findDistributorByClientAddress'])
            ->name('distribuidores.por-endereco-cliente');

        // Rotas de recurso – as rotas que usam {id} são restringidas para aceitar somente números
        Route::get('/', [DistribuidorController::class, 'index'])
            ->name('distribuidores.index');

        Route::get('/{id}', [DistribuidorController::class, 'show'])
            ->name('distribuidores.show')
            ->where('id', '[0-9]+');

        Route::post('/', [DistribuidorController::class, 'store'])
            ->name('distribuidores.store');

        Route::put('/{id}', [DistribuidorController::class, 'update'])
            ->name('distribuidores.update')
            ->where('id', '[0-9]+');

        Route::delete('/{id}', [DistribuidorController::class, 'destroy'])
            ->name('distribuidores.destroy')
            ->where('id', '[0-9]+');
    });
    //ENTREGADORES
Route::group(['prefix' => 'entregadores', 'middleware' => 'auth'], function () {
    Route::get('/', [EntregadorController::class, 'index'])->name('entregadores.index');
    Route::post('/', [EntregadorController::class, 'store'])->name('entregadores.store');
    Route::get('/{id}', [EntregadorController::class, 'show'])->name('entregadores.show');
    Route::put('/{id}', [EntregadorController::class, 'update'])->name('entregadores.update');
    Route::delete('/{id}', [EntregadorController::class, 'destroy'])->name('entregadores.destroy');
});

    //CATEGORIAS
    Route::resource('categorias', CategoriaController::class, ['except' => 'create']);

    //PRECO
    Route::group(['prefix' => 'preco'], function () {
        Route::get('/', [PrecoController::class, 'index']);
        Route::post('/', [PrecoController::class, 'store']);
        Route::get('/{idProduto}', [PrecoController::class, 'show']);
        Route::put('/', [PrecoController::class, 'update']);
        Route::delete('/{preco}', [PrecoController::class, 'destroy']);
    });

    //RELATORIOS
    Route::group(['prefix' => 'relatorio'], function () {
        Route::post('pedidos', [PedidoController::class, 'relatorioPedidos']);
        Route::post('vendas', [PedidoController::class, 'relatorioVendas']);
        Route::post('vendasProduto', [PedidoController::class, 'relatorioVendasProduto']);
        Route::post('vendasEntregador', [PedidoController::class, 'relatorioVendasEntregador']);
        Route::post('estoque', [EstoqueController::class, 'relatorioEstoque']);
    });

    //ESTOQUE
    Route::group(['prefix' => 'estoque'], function () {
        Route::get('/', [EstoqueController::class, 'index'])->name('estoque.index');
        Route::put('/{id}', [EstoqueController::class, 'update'])->name('estoque.update');
    });

    Route::post('/stock-unions', [StockUnionController::class, 'createUnion'])->name('stock-unions.create');

    //REMINDER
    Route::apiResource('reminders', ReminderController::class);

});



Route::get('/homepage', [HomeController::class, 'getHomepage'])->name('homepage');

require __DIR__ . '/auth.php';
require __DIR__ . '/cliente.php';
