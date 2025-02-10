<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\ItemPedido;
use App\Models\Distribuidor;
use App\Models\Entregador;
use App\Models\Administrador;

class ShowPedidoAction extends BasePedidoAction
{
    public function execute($id)
    {
        $pedido = Pedido::withBasicRelations()->withFormattedDates()->find($id);

        $u = auth()->user();
        if ($u->tipoAdministrador == 'Distribuidor' && $u->idDistribuidor != $pedido->idDistribuidor) {
            return false;
        }

        $pedido->itensPedido = ItemPedido::where('idPedido', $id)
            ->where('qtd', "!=", 0)
            ->with('produto')
            ->get();

        $pedido->clientePedido = Cliente::find($pedido->endereco->idCliente);
        $pedido->distribuidor = Distribuidor::select('distribuidor.nome', 'distribuidor.dddTelefone', 'distribuidor.telefonePrincipal')
            ->find($pedido->idDistribuidor);
        $pedido->entregador = Entregador::select('nome', 'telefone')
            ->find($pedido->idEntregador);

        if ($pedido->idAdministrador != null) {
            $administrador = Administrador::select('nome')->find($pedido->idAdministrador);
            $pedido->administrador = $administrador->nome;
        }

        return $pedido;
    }
}
