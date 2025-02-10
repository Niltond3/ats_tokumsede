<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;

class UltimoPedidoAction extends BasePedidoAction
{
    public function execute()
    {
        $user = auth()->user();

        $query = Pedido::query();

        if ($user->tipoAdministrador !== "Administrador" && $user->tipoAdministrador !== "Atendente") {
            $query->where('idDistribuidor', $user->idDistribuidor);
        }

        $ultimoPedido = $query->orderBy("pedido.id", "DESC")
            ->first();

        return $ultimoPedido->id;
    }
}
