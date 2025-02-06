<?php
include 'cors.php';

use App\Http\Controllers\Api\IndexController as Api;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DistribuidorController;
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

Route::get('/home', function () {
    return Inertia::render('Home');
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
                'update' => 'produtos.update',
                'destroy' => 'produtos.destroy'
            ]);
        Route::get('listarProdutos/{idDistribuidor}/{idCliente}', [ProdutoController::class, 'listarProdutos'])->name('produtos.listar');
        Route::get('listarPorDistribuidor/{idDistribuidor}/{idCliente?}', [ProdutoController::class, 'showByDistribuidor'])->name('produtos.por-distribuidor');
        Route::get('{idEnderecoCliente}', [ProdutoController::class, 'show'])->name('produtos.show-by-endereco');
        Route::put('status/{idProduto}/{idStatus}', [ProdutoController::class, 'updateStatus'])->name('produtos.status.update');


    });

    //DISTRIBUIDORES
    Route::group(['prefix' => 'distribuidores'], function () {
        Route::get('/all', [DistribuidorController::class, 'getAllDistributors'])->name('distribuidores.todos');
        Route::get('/{id}', [DistribuidorController::class, 'show'])->name('distribuidores.show');
        Route::resource('/', DistribuidorController::class, ['except' => ['create', 'show']])
            ->names([
                'index' => 'distribuidores.index',
                'store' => 'distribuidores.store',
                'update' => 'distribuidores.update',
                'destroy' => 'distribuidores.destroy',
                'edit' => 'distribuidores.edit'
            ]);
        Route::get('/by-address/{addressId}', [DistribuidorController::class, 'findDistributorByAddress'])->name('distribuidores.por-endereco');
        Route::get('/by-client-address/{clientAddressId}', [DistribuidorController::class, 'findDistributorByClientAddress'])->name('distribuidores.por-endereco-cliente');
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


// //API
// Route::group(['prefix' => 'api'], function () {
    // Route::resource('/', Api::class, ['except' => 'create'])
    //     ->names([
    //         'index' => 'api.index',
    //         'show' => 'api.show',
    //         'store' => 'api.store',
    //         'edit' => 'api.edit',
    //         'update' => 'api.update',
    //         'destroy' => 'api.destroy'
    //     ]);
//     // Demais rotas configuradas
//     Route::get('verificaPedidoAlterado', [Api::class, 'verificaPedidoAlterado']);
//     Route::get('verificaEmail', [Api::class, 'verificaEmail']);
//     Route::get('consultaInicial', [Api::class, 'consultaInicial']);
//     Route::get('solicitaContato', [Api::class, 'solicitaContato']);
//     Route::get('enviaEmail', [Api::class, 'enviaEmail']);
//     Route::get('removerEndereco', [Api::class, 'removerEndereco']);
//     Route::get('listImages', [Api::class, 'listImages']);
//     Route::get('consultaInicialSemCadastro', [Api::class, 'consultaInicialSemCadastro']);
//     Route::get('clientePotencial', [Api::class, 'clientePotencial']);
//     Route::get('login', [Api::class, 'login']);
//     Route::get('refreshRegId', [Api::class, 'refreshRegId']);
//     Route::get('notificacaoRecebida', [Api::class, 'notificacaoRecebida']);
//     Route::get('senhaModoTeste', [Api::class, 'senhaModoTeste']);
//     Route::get('alteraEnderecoAtual', [Api::class, 'alteraEnderecoAtual']);
//     Route::get('cadastrarNovoEndereco', [Api::class, 'cadastrarNovoEndereco']);
//     Route::get('cancelarPedido', [Api::class, 'cancelarPedido']);
//     Route::get('pedidoRecebido', [Api::class, 'pedidoRecebido']);
//     Route::get('alteraDadosCliente', [Api::class, 'alteraDadosCliente']);
//     Route::get('verifyRecover', [Api::class, 'verifyRecover']);
//     Route::get('alteraSenha', [Api::class, 'alteraSenha']);
//     Route::get('novoPedido', [Api::class, 'novoPedido']);
// });


require __DIR__ . '/auth.php';
require __DIR__ . '/cliente.php';
