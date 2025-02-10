<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\EnderecoCliente;
use App\Models\Cliente;
use Illuminate\Http\Request;

class AceitarPedidoAction extends BasePedidoAction
{
    public function execute(Request $request, $idPedido)
    {
        $pedido = Pedido::find($idPedido);

        if ($pedido->status != Pedido::PENDENTE) {
            return response("Esse pedido foi cancelado pelo usuário ou já foi aceito por outro administrador!", 400);
        }

        $pedido->statusChange = 1;
        $enderecoCliente = EnderecoCliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        $pedido->status = Pedido::ACEITO;
        $pedido->aceitoPor = auth()->user()->nome;
        $pedido->horarioAceito = now()->format('Y-m-d H:i:s');

        if ($pedido->save()) {
            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }
            return response($pedido->id, 200);
        }

        return response("Erro ao aceitar o pedido. Tente novamente ou contate o suporte.", 400);
    }
}
