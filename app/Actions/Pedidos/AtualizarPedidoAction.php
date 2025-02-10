<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Cliente;
use App\Models\Distribuidor;
use App\Models\Administrador;
use App\Models\EnderecoCliente;
use Illuminate\Http\Request;

class AtualizarPedidoAction extends BasePedidoAction
{
    public function execute(Request $request, $idPedido)
    {
        $pedido = Pedido::find($idPedido);
        $this->prepareRequestData($request);

        if ($request->idDistribuidor != $pedido->idDistribuidor) {
            $this->handleDistribuidorChange($pedido, $request);
        }

        $this->updatePedidoItems($request, $idPedido, $pedido);

        return $pedido->id;
    }

    private function prepareRequestData(&$request)
    {
        $request['dataAgendada'] = $request->dataAgendada == "" ? null :
            implode("-", array_reverse(explode("/", $request->dataAgendada)));
        $request['trocoPara'] = $request->trocoPara ?: 0;
    }

    private function handleDistribuidorChange($pedido, $request)
    {
        $cliente = Cliente::find($pedido->endereco->idCliente);
        $this->updateClienteRating($cliente, $pedido);

        $pedido->status = Pedido::PENDENTE;
        $this->notifyDistribuidorChange($pedido, $request, $cliente);
    }

    private function updateClienteRating($cliente, $pedido)
    {
        switch($pedido->status) {
            case Pedido::ENTREGUE:
                $cliente->rating--;
                break;
            case Pedido::CANCELADO_NAO_LOCALIZADO:
                $cliente->rating++;
                break;
            case Pedido::CANCELADO_TROTE:
                $cliente->rating += 3;
                break;
        }
        $cliente->save();
    }

    private function updatePedidoItems(Request $request, $idPedido, $pedido)
    {
        // Reset existing items
        ItemPedido::where('idPedido', $idPedido)
            ->update(['qtd' => 0, 'preco' => 0, 'subtotal' => 0]);

        // Update with new items
        foreach ($request->itens as $item) {
            ItemPedido::updateOrCreate(
                ['idPedido' => $idPedido, 'idProduto' => $item['idProduto']],
                [
                    'qtd' => $item['quantidade'],
                    'preco' => $item['preco'],
                    'subtotal' => $item['subtotal']
                ]
            );
        }

        $pedido->update($request->all());
    }
}
