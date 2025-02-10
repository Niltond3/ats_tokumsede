<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\Distribuidor;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RelatorioVendasAction extends BasePedidoAction
{
    public function execute(Request $request)
    {
        $user = auth()->user();
        $request['dataInicial'] = implode("-", array_reverse(explode("/", $request->dataInicial)));
        $request['dataFinal'] = implode("-", array_reverse(explode("/", $request->dataFinal)));

        $filtroData = $this->buildDateFilter($request);
        $complementoSql = $this->buildDistribuidorFilter($request);

        $pedidos = $this->getPedidosQuery($user, $filtroData, $complementoSql);

        $valorTotalGeral = 0;
        foreach ($pedidos as $pedido) {
            setlocale(LC_MONETARY, "pt_BR", "ptb");
            $valorTotalGeral += $pedido->valorTotal;
            $pedido->valorTotal = 'R$ ' . number_format($pedido->valorTotal, 2, ',', '.');
        }

        $valorTotalGeral = 'R$ ' . number_format($valorTotalGeral, 2, ',', '.');

        return [$pedidos, $valorTotalGeral];
    }

    private function buildDateFilter($request)
    {
        $filtroData = "";
        if ($request->dataInicial) {
            $filtroData .= "and pedido.horarioEntrega >= '$request->dataInicial 00:00:00'";
        }
        if ($request->dataFinal) {
            $filtroData .= "and pedido.horarioEntrega <= '$request->dataFinal 23:59:59'";
        }
        return $filtroData;
    }

    private function buildDistribuidorFilter($request)
    {
        $complementoSql = "";
        if ($request->idDistribuidores) {
            $escolhidos = explode(",", $request->idDistribuidores);
            $complementoSql .= " and (";

            foreach ($escolhidos as $i => $distribuidorId) {
                if ($i > 0) {
                    $complementoSql .= " or ";
                }
                $complementoSql .= "pedido.idDistribuidor = " . $distribuidorId;
            }

            $complementoSql .= ") ";
        }
        return $complementoSql;
    }

    private function getPedidosQuery($user, $filtroData, $complementoSql)
    {
        $baseQuery = DB::table('pedido')
            ->join('distribuidor', 'pedido.idDistribuidor', '=', 'distribuidor.id')
            ->select(DB::raw("pedido.idDistribuidor as idDist, distribuidor.nome as distribuidor, date_format(pedido.horarioEntrega, '%d/%m/%Y') as dataEntrega, sum(pedido.total) as valorTotal"));

        if ($user->tipoAdministrador == "Distribuidor") {
            $baseQuery->whereRaw("pedido.status = 7 $filtroData $complementoSql and pedido.idDistribuidor = " . $user->idDistribuidor);
        } else {
            $baseQuery->whereRaw("pedido.status = 7 $filtroData $complementoSql");
        }

        return $baseQuery->groupBy('idDist', 'dataEntrega', 'distribuidor')->get();
    }
}
