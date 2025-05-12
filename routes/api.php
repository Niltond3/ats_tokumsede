<?php

use App\Http\Controllers\Api\IndexController as ApiController;
use App\Http\Controllers\Api\UtilController;

Route::middleware(['throttle:60,1'])->group(function () {
    // Main API resource routes
    Route::apiResource('/', ApiController::class)->names([
        'index' => 'api.index',
        'show' => 'api.show',
        'store' => 'api.store',
        'edit' => 'api.edit',
        'update' => 'api.update',
        'destroy' => 'api.destroy'
    ]);

    // Util routes
    Route::apiResource('util', UtilController::class);

    // Individual API endpoints
    Route::controller(ApiController::class)->group(function () {
        Route::get('verificaPedidoAlterado', 'verificaPedidoAlterado');
        Route::get('verificaEmail', 'verificaEmail');
        Route::get('consultaInicial', 'consultaInicial');
        Route::get('solicitaContato', 'solicitaContato');
        Route::get('enviaEmail', 'enviaEmail');
        Route::get('removerEndereco', 'removerEndereco');
        Route::get('listImages', 'listImages');
        Route::get('consultaInicialSemCadastro', 'consultaInicialSemCadastro');
        Route::get('clientePotencial', 'clientePotencial');
        Route::get('login', 'login');
        Route::get('refreshRegId', 'refreshRegId');
        Route::get('notificacaoRecebida', 'notificacaoRecebida');
        Route::get('senhaModoTeste', 'senhaModoTeste');
        Route::get('alteraEnderecoAtual', 'alteraEnderecoAtual');
        Route::get('cadastrarNovoEndereco', 'cadastrarNovoEndereco');
        Route::get('cadastraCliente', 'cadastraCliente');
        Route::get('cancelarPedido', 'cancelarPedido');
        Route::get('pedidoRecebido', 'pedidoRecebido');
        Route::get('alteraDadosCliente', 'alteraDadosCliente');
        Route::get('verifyRecover', 'verifyRecover');
        Route::get('alteraSenha', 'alteraSenha');
        Route::get('novoPedido', 'novoPedido');
    });
});
