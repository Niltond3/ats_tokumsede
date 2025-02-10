<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\EnderecoCliente;
use App\Models\Cliente;
use App\Models\Entregador;
use Illuminate\Http\Request;

class DespacharPedidoAction extends BasePedidoAction
{
    public function execute(Request $request, $idPedido)
    {
        $pedido = Pedido::find($idPedido);

        if ($pedido->status != Pedido::ACEITO) {
            return response("Esse pedido foi cancelado pelo usuário ou não estava mais como aceito!", 400);
        }

        $pedido->statusChange = 1;
        if ($request->entregador) {
            $pedido->idEntregador = $request->entregador;
        } else {
            $pedido->idEntregador = Entregador::where("nome", auth()->user()->nome)
                ->select('id')
                ->first()
                ->id;
        }

        $enderecoCliente = EnderecoCliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        $pedido->status = Pedido::DESPACHADO;
        $pedido->despachadoPor = auth()->user()->nome;
        $pedido->horarioDespache = now()->format('Y-m-d H:i:s');

        if ($pedido->save()) {
            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }
            return response($pedido->id, 200);
        }

        return response("Erro ao despachar o pedido. Tente novamente ou contate o administrador.", 400);
    }
}
