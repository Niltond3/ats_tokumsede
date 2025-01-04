<?php
include 'cors.php';

use App\Http\Controllers\Api\IndexController as Api;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DistribuidorController;
use App\Http\Controllers\EnderecoClienteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PrecoController;
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

Route::get('/home/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/home/register/produto', function () {
    return Inertia::render('Home');
})->middleware(['auth', 'verified'])->name('novo-produto');

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
        Route::get('listarPorDistribuidor/{idDistribuidor}', [ProdutoController::class, 'showByDistribuidor'])->name('produtos.por-distribuidor');
        Route::get('{idEnderecoCliente}', [ProdutoController::class, 'show'])->name('produtos.show-by-endereco');
    });

    //DISTRIBUIDORES
    Route::group(['prefix' => 'distribuidores'], function () {
        Route::resource('/', DistribuidorController::class, ['except' => 'create'])
            ->names([
                'index' => 'distribuidores.index',
                'show' => 'distribuidores.show',
                'store' => 'distribuidores.store',
                'edit' => 'distribuidores.edit',
                'update' => 'distribuidores.update',
                'destroy' => 'distribuidores.destroy'
            ]);
        Route::get('todosOsDistribuidores', [DistribuidorController::class, 'getAllDistributors'])->name('distribuidores.listar');
        Route::get('listarPorEndereco/{idEndereco}', [DistribuidorController::class, 'findDistributorByAddress'])->name('distribuidores.por-endereco');
        Route::get('listarPorEnderecoCliente/{idEndereco}', [DistribuidorController::class, 'findDistributorByClientAddress'])->name('distribuidores.por-endereco-cliente');

    });

    //CATEGORIAS
    Route::resource('categorias', CategoriaController::class, ['except' => 'create']);

    //PRECO
    Route::resource('preco', PrecoController::class, ['except' => 'create']);
    Route::put('/preco', [PrecoController::class, 'update']);
    
});



Route::get('/homepage', [HomeController::class, 'getHomepage'])->name('homepage');


//API
Route::group(['prefix' => 'api'], function () {
    Route::resource('/', Api::class, ['except' => 'create'])
        ->names([
            'index' => 'api.index',
            'show' => 'api.show',
            'store' => 'api.store',
            'edit' => 'api.edit',
            'update' => 'api.update',
            'destroy' => 'api.destroy'
        ]);
    // Demais rotas configuradas
    Route::get('verificaPedidoAlterado', [Api::class, 'verificaPedidoAlterado']);
    Route::get('verificaEmail', [Api::class, 'verificaEmail']);
    Route::get('consultaInicial', [Api::class, 'consultaInicial']);
    Route::get('solicitaContato', [Api::class, 'solicitaContato']);
    Route::get('enviaEmail', [Api::class, 'enviaEmail']);
    Route::get('removerEndereco', [Api::class, 'removerEndereco']);
    Route::get('listImages', [Api::class, 'listImages']);
    Route::get('consultaInicialSemCadastro', [Api::class, 'consultaInicialSemCadastro']);
    Route::get('clientePotencial', [Api::class, 'clientePotencial']);
    Route::get('login', [Api::class, 'login']);
    Route::get('refreshRegId', [Api::class, 'refreshRegId']);
    Route::get('notificacaoRecebida', [Api::class, 'notificacaoRecebida']);
    Route::get('senhaModoTeste', [Api::class, 'senhaModoTeste']);
    Route::get('alteraEnderecoAtual', [Api::class, 'alteraEnderecoAtual']);
    Route::get('cadastrarNovoEndereco', [Api::class, 'cadastrarNovoEndereco']);
    Route::get('cancelarPedido', [Api::class, 'cancelarPedido']);
    Route::get('pedidoRecebido', [Api::class, 'pedidoRecebido']);
    Route::get('alteraDadosCliente', [Api::class, 'alteraDadosCliente']);
    Route::get('verifyRecover', [Api::class, 'verifyRecover']);
    Route::get('alteraSenha', [Api::class, 'alteraSenha']);
    Route::get('novoPedido', [Api::class, 'novoPedido']);
});


require __DIR__ . '/auth.php';
require __DIR__ . '/cliente.php';
