<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Cliente;
use App\Models\Distribuidor;
use App\Models\Entregador;
use App\Models\Administrador;

class EditarPedidoAction extends BasePedidoAction
{
    public function execute($id)
    {
        $pedido = Pedido::selectRaw("pedido.*,
            CONCAT('', REPLACE(REPLACE(REPLACE(FORMAT((pedido.trocoPara - pedido.total), 2),'.',';'),',','.'),';',',')) AS troco,
            date_format(pedido.horarioPedido, '%d/%m/%Y %H:%i') as horarioPedido,
            date_format(pedido.horarioAceito, '%d/%m/%Y %H:%i') as horarioAceito,
            date_format(pedido.horarioDespache, '%d/%m/%Y %H:%i') as horarioDespache,
            date_format(pedido.horarioEntrega, '%d/%m/%Y %H:%i') as horarioEntrega,
            date_format(pedido.dataAgendada, '%d/%m/%Y') as dataAgendada")
            ->find($id);

        $user = auth()->user();
        if (!$this->userCanEdit($user, $pedido)) {
            return false;
        }

        return $this->loadPedidoRelations($pedido);
    }

    private function userCanEdit($user, $pedido)
    {
        return $user->tipoAdministrador == 'Administrador'
            || $user->id == $pedido->idAdministrador
            || in_array($user->id, [50, 51, 61, 48, 96]);
    }

    private function loadPedidoRelations($pedido)
    {
        $pedido->itensPedido = ItemPedido::where('idPedido', $pedido->id)
            ->where('qtd', "!=", 0)
            ->with('produto')
            ->get();

        $pedido->clientePedido = Cliente::find($pedido->endereco->idCliente);

        $pedido->distribuidor = Distribuidor::select('distribuidor.id', 'distribuidor.nome', 'distribuidor.dddTelefone', 'distribuidor.telefonePrincipal')
            ->find($pedido->idDistribuidor);

        $pedido->entregador = Entregador::select('nome', 'telefone')
            ->find($pedido->idEntregador);

        $distribuidores = Distribuidor::where('status', '!=', 3)->get();

        if ($pedido->idAdministrador) {
            $administrador = Administrador::select('nome')->find($pedido->idAdministrador);
            $pedido->administrador = $administrador->nome;
        }

        return [$pedido, $distribuidores];
    }
}
