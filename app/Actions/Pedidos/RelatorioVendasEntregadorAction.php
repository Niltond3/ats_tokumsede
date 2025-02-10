<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RelatorioVendasEntregadorAction extends BasePedidoAction
{
    public function execute(Request $request)
    {
        $user = auth()->user();
        $request['dataInicial'] = implode("-", array_reverse(explode("/", $request->dataInicial)));
        $request['dataFinal'] = implode("-", array_reverse(explode("/", $request->dataFinal)));

        $filtroData = $this->buildDateFilter($request);
        $complementoSql = $this->buildFilters($request);

        $pedidos = $this->getPedidosQuery($user, $filtroData, $complementoSql);

        return $this->formatResults($pedidos);
    }

    private function buildDateFilter($request)
    {
        $filtroData = "";
        if ($request->dataInicial) {
            $filtroData .= "and pedido.horarioEntrega >= '$request->dataInicial 00:00:00'";
        }
        if ($request->dataFinal) {
            $filtroData .= $filtroData == "" ?
                " pedido.horarioEntrega <= '$request->dataFinal 23:59:59'" :
                "AND pedido.horarioEntrega <= '$request->dataFinal 23:59:59'";
        }
        return $filtroData;
    }

    private function buildFilters($request)
    {
        $complementoSql = "";

        if ($request->idEntregadores) {
            $escolhidos = explode(",", $request->idEntregadores);
            $complementoSql .= " and (";
            foreach ($escolhidos as $i => $entregadorId) {
                if ($i > 0) $complementoSql .= " or ";
                $complementoSql .= "pedido.idEntregador = " . $entregadorId;
            }
            $complementoSql .= ") ";
        }

        if ($request->selectProdutos) {
            $escolhidos = explode(",", $request->selectProdutos);
            $complementoSql .= " and (";
            foreach ($escolhidos as $i => $produtoId) {
                if ($i > 0) $complementoSql .= " or ";
                $complementoSql .= "itemPedido.idProduto = " . $produtoId;
            }
            $complementoSql .= ") ";
        }

        return $complementoSql;
    }

    private function getPedidosQuery($user, $filtroData, $complementoSql)
    {
        $baseQuery = DB::table('pedido')
            ->from(DB::raw("(((itemPedido itemPedido
                left join pedido pedido on itemPedido.idPedido = pedido.id)
                left join distribuidor distribuidor on pedido.idDistribuidor = distribuidor.id)
                left join entregador entregador on pedido.idEntregador = entregador.id)
                left join produto produto on itemPedido.idProduto = produto.id"))
            ->select(DB::raw("itemPedido.idProduto as idProd,
                itemPedido.precoAcertado as precoAcertado,
                produto.nome as produto,
                distribuidor.nome as distribuidor,
                entregador.nome as entregador,
                sum(itemPedido.subtotal) as valorTotal,
                sum(itemPedido.qtd) as quantidadeTotal"));

        if ($user->tipoAdministrador == "Distribuidor") {
            $baseQuery->whereRaw("pedido.status = 7 $filtroData $complementoSql
                and pedido.idDistribuidor = " . $user->idDistribuidor);
        } else {
            $baseQuery->whereRaw("pedido.status = 7 $filtroData $complementoSql");
        }

        return $baseQuery->groupBy('idProd', 'distribuidor', 'entregador', 'produto', 'precoAcertado')->get();
    }

    private function formatResults($pedidos)
    {
        $valorTotalGeral = 0;
        foreach ($pedidos as $pedido) {
            setlocale(LC_MONETARY, "pt_BR", "ptb");
            $valorTotalGeral += $pedido->valorTotal;
            $pedido->valorTotal = 'R$ ' . number_format($pedido->valorTotal, 2, ',', '.');
        }

        $valorTotalGeral = 'R$ ' . number_format($valorTotalGeral, 2, ',', '.');

        return [$pedidos, $valorTotalGeral];
    }
}
