<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\EnderecoCliente;
use App\Models\Cliente;
use Illuminate\Http\Request;

class CancelarPedidoAction extends BasePedidoAction
{
    public function execute(Request $request, $idPedido)
    {
        $pedido = Pedido::find($idPedido);
        $pedido->statusChange = 1;
        $pedido->save();

        $enderecoCliente = EnderecoCliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        if ($request->motivo == Pedido::CANCELADO_NAO_LOCALIZADO) {
            $pedido->status = Pedido::CANCELADO_NAO_LOCALIZADO;
            $cliente->rating = $cliente->rating - 1;
        } else if ($request->motivo == Pedido::CANCELADO_TROTE) {
            $pedido->status = Pedido::CANCELADO_TROTE;
            $cliente->rating = $cliente->rating - 3;
        }

        $pedido->horarioCancelado = now()->format('Y-m-d H:i:s');
        $pedido->canceladoPor = auth()->user()->nome;

        if ($pedido->save()) {
            $cliente->save();
            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }
            return response($pedido->id, 200);
        }

        return response("Erro ao cancelar o pedido. Tente novamente ou contate o administrador.", 400);
    }
}
