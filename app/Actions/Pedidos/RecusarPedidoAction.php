<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\EnderecoCliente;
use App\Traits\UsesNotificationService;

class RecusarPedidoAction extends BasePedidoAction
{
    use UsesNotificationService;

    public function __construct()
    {
        $this->initializeNotificationService();
    }

    public function execute($idPedido, $request)
    {
        $date = now();
        $pedido = Pedido::find($idPedido);
        $pedido->statusChange = 1;
        $pedido->save();

        $enderecoCliente = EnderecoCliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        $pedido->status = Pedido::RECUSADO;
        $pedido->retorno = $request->retorno;
        $pedido->canceladoPor = auth()->user()->nome;
        $pedido->horarioCancelado = $date->format('Y-m-d H:i:s');

        if ($pedido->save()) {
            if ($cliente->regId) {
                $this->gcmSend(
                    $cliente->regId,
                    $cliente->id,
                    $idPedido,
                    $pedido->status,
                    $pedido->retorno,
                    $pedido->origem,
                    true
                );
            }
            return response($pedido->id, 200);
        }

        return response("Erro ao recusar o pedido. Tente novamente ou contate o administrador.", 400);
    }
}
