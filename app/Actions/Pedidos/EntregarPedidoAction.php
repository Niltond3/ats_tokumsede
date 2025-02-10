<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\EnderecoCliente;
use App\Models\Distribuidor;
use App\Models\ItemPedido;
use App\Traits\UsesDistributorService;
use App\Traits\UsesStockService;
use App\Traits\UsesNotificationService;

class EntregarPedidoAction extends BasePedidoAction
{
    use UsesDistributorService, UsesStockService, UsesNotificationService;

    public function __construct()
    {
        $this->initializeDistributorService();
        $this->initializeStockService();
        $this->initializeNotificationService();
    }

    public function execute($idPedido)
    {
        $pedido = Pedido::find($idPedido);
        $effectiveDistributorId = $this->getEffectiveDistributorId($pedido->idDistribuidor);

        $pedido->statusChange = 1;
        $pedido->save();

        $distribuidor = Distribuidor::find($pedido->idDistribuidor);
        $itensPedido = ItemPedido::with('produto')->where("idPedido", $idPedido)->get();

        if ($pedido->status == Pedido::DESPACHADO) {
            foreach ($itensPedido as $itemPedido) {
                $this->atualizaEstoque($effectiveDistributorId, $itemPedido->Produto, $itemPedido->qtd, false);
            }
            $this->atualizaComposicoes($effectiveDistributorId);
        }

        $enderecoCliente = EnderecoCliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        $cliente->rating = $cliente->rating + 1;

        $pedido->status = Pedido::ENTREGUE;
        $pedido->entreguePor = auth()->user()->nome;
        $pedido->horarioEntrega = now()->format('Y-m-d H:i:s');

        if ($pedido->save()) {
            $cliente->save();
            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }
            return response($pedido->id, 200);
        }

        return response("Erro ao entregar o pedido. Tente novamente ou contate o administrador.", 400);
    }
}
