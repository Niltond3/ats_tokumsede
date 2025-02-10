<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\EnderecoCliente;
use App\Models\Cliente;

class SetPendentePedidoAction extends BasePedidoAction
{
    public function execute($idPedido)
    {
        $pedido = Pedido::find($idPedido);
        $pedido->statusChange = 1;
        $pedido->save();

        $enderecoCliente = EnderecoCliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        $pedido->status = Pedido::PENDENTE;
        $pedido->editadoPor = auth()->user()->nome;
        $pedido->horarioAceito = null;
        $pedido->horarioDespache = null;
        $pedido->horarioEntrega = null;
        $pedido->horarioCancelado = null;
        $pedido->aceitoPor = null;
        $pedido->despachadoPor = null;
        $pedido->entreguePor = null;
        $pedido->canceladoPor = null;
        $pedido->idEntregador = null;
        $pedido->agendado = 0;
        $pedido->dataAgendada = null;
        $pedido->horaInicio = null;
        $pedido->horaFim = null;

        if ($pedido->save()) {
            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }
            return response($pedido->id, 200);
        }

        return response("Erro ao retornar pedido para pendente. Tente novamente.", 400);
    }
}
