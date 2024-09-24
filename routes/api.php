<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IndexController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::apiResource('index', IndexController::class);
Route::get('senhaModoTeste', 'Api\IndexController@senhaModoTeste');
Route::get('testeComposicao', 'Api\IndexController@testeComposicao');
Route::get('testeSelect', 'Api\IndexController@testeSelect');
Route::get('clientePotencial', 'Api\IndexController@clientePotencial');
Route::get('solicitaContato', 'Api\IndexController@solicitaContato');
Route::get('senhaModoTeste', 'Api\IndexController@senhaModoTeste');
Route::get('selectDist', 'Api\IndexController@selectDist');
Route::get('certificadoImpressora', 'Api\IndexController@certificadoImpressora');
Route::get('testePedido', 'Api\IndexController@testePedido');
Route::get('gcmStatusPedido', 'Api\IndexController@gcmStatusPedido');
Route::get('verificaPedidoAlterado', 'Api\IndexController@verificaPedidoAlterado');
Route::get('notificacaoRecebida', 'Api\IndexController@notificacaoRecebida');
Route::get('refreshRegId', 'Api\IndexController@refreshRegId');
Route::get('verifyRecover', 'Api\IndexController@verifyRecover');
Route::get('alteraSenha', 'Api\IndexController@alteraSenha');
Route::get('testeEmail', 'Api\IndexController@testeEmail');
Route::get('enviaEmail', 'Api\IndexController@enviaEmail');
Route::get('pedidoRecebido', 'Api\IndexController@pedidoRecebido');
Route::get('cancelarPedido', 'Api\IndexController@cancelarPedido');
Route::get('novoPedido', 'Api\IndexController@novoPedido');
Route::get('verificaEmail', 'Api\IndexController@verificaEmail');
Route::get('cadastraCliente', 'Api\IndexController@cadastraCliente');
Route::get('alteraEnderecoAtual', 'Api\IndexController@alteraEnderecoAtual');
Route::get('alteraDadosCliente', 'Api\IndexController@alteraDadosCliente');
Route::get('login', 'Api\IndexController@login');
Route::get('cadastrarNovoEndereco', 'Api\IndexController@cadastrarNovoEndereco');
Route::get('listImages', 'Api\IndexController@listImages');
Route::get('listUnusedImages', 'Api\IndexController@listUnusedImages');
Route::get('removerEndereco', 'Api\IndexController@removerEndereco');
Route::get('consultaInicial', 'Api\IndexController@consultaInicial');
Route::get('duplicaProdutos', 'Api\IndexController@duplicaProdutos');
Route::get('consultaInicialSemCadastro', 'Api\IndexController@consultaInicialSemCadastro');
Route::get('consultaCoords', 'Api\IndexController@consultaCoords');
Route::get('calcDistancia', 'Api\IndexController@calcDistancia');
Route::get('buscarLatitudeLongitude', 'Api\IndexController@buscarLatitudeLongitude');
