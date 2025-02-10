<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\ItemPedido;
use Illuminate\Http\Request;

class UpdatePedidoAction extends BasePedidoAction
{
    public function execute(Request $request, $id)
    {
        $pedido = Pedido::find($id);
        $pedido->status = $request->status;

        if ($pedido->horarioEntrega == null) {
            $pedido->horarioEntrega = now()->format('Y-m-d H:i:s');
        }

        $pedido->save();
        return response($pedido->id, 200);
    }
}
