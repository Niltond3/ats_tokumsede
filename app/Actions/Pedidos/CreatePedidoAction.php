<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\Cliente;
use App\Models\Distribuidor;
use App\Models\Administrador;
use Illuminate\Http\Request;

class CreatePedidoAction extends BasePedidoAction
{
    public function execute(Request $request)
    {
        $dataAgendada = $request->dataAgendada == "" ? null : implode("-", array_reverse(explode("/", $request->dataAgendada)));
        $idAdministrador = auth()->user()->id;

        $effectiveDistributorId = $this->getEffectiveDistributorId($request->idDistribuidor);

        $pedido = new Pedido($request->all());
        $pedido->idDistribuidor = $effectiveDistributorId;
        $pedido->trocoPara = $request->trocoPara ? $request->trocoPara : 0;
        $pedido->obs = $request->obs ? $request->obs : "";
        $pedido->horarioPedido = date('Y-m-d H:i:s');
        $pedido->status = Pedido::PENDENTE;
        $pedido->origem = $request->origem ? $request->origem : Pedido::PLATAFORMA;
        $pedido->dataAgendada = $dataAgendada;
        $pedido->idAdministrador = $request->origem ? null : $idAdministrador;

        if ($pedido->save()) {
            $this->createOrderItems($request->itens, $pedido->id);
            $this->sendNotifications($pedido, $effectiveDistributorId, $idAdministrador);
            return $pedido->id;
        }

        return response("Erro ao cadastrar o pedido. Tente novamente ou contate a central.");
    }

    private function createOrderItems($itens, $pedidoId) {
        foreach ($itens as $item) {
            $itemPedido = new ItemPedido();
            $itemPedido->idPedido = $pedidoId;
            $itemPedido->idProduto = $item['idProduto'];
            $itemPedido->qtd = $item['quantidade'];
            $itemPedido->preco = $item['preco'];
            $itemPedido->subtotal = $item['subtotal'];
            $itemPedido->save();
        }
    }

    private function sendNotifications($pedido, $distributorId, $adminId) {
        $distribuidor = Distribuidor::find($distributorId);
        $administradores = Administrador::where([
            ['idDistribuidor', $distributorId],
            ['status', 'Ativo'],
            ['id', '!=', $adminId]
        ])
        ->orWhere([
            ['tipoAdministrador', 'Administrador'],
            ['status', 'Ativo'],
            ['id', '!=', $adminId]
        ])
        ->get();

        $endereco = $pedido->endereco;
        $cliente = Cliente::find($endereco->idCliente);

        $msg = [
            'title' => "Pedido {$pedido->id} - {$cliente->nome}",
            'body' => "{$endereco->logradouro} {$endereco->numero}, {$endereco->bairro} - {$endereco->cidade}/{$endereco->estado}",
            'icon' => '/images/logo-icon.png',
            'click_action' => 'https://tks.tokumsede.com.br'
        ];

        $this->fcmService->sendOrderNotification($administradores, $msg);
    }
        /**
 * Gets the effective distributor ID considering stock unions
 * @param int $distributorId Original distributor ID
 * @return int Main distributor ID or original ID if no union exists
 */
private function getEffectiveDistributorId($distributorId) {
    $distributor = Distribuidor::find($distributorId);
    return $distributor->getMainDistributorIdAttribute() ?? $distributorId;
}
}
