<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\Entregador;
use Illuminate\Http\Request;

class RelatorioPedidosAction extends BasePedidoAction
{
    public function execute(Request $request)
    {
        if (!auth()->check()) {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }

        $queries = [
            'pendentes' => Pedido::withBasicRelations()->withFormattedDates()
                ->whereRaw("((pedido.agendado = 1 AND (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) <= 30)
                OR DATE(pedido.dataAgendada) < CURDATE()) OR pedido.agendado = 0)")
                ->where('status', Pedido::PENDENTE),
            'aceitos' => Pedido::withBasicRelations()->withFormattedDates()->where('status', Pedido::ACEITO),
            'despachados' => Pedido::withBasicRelations()->withFormattedDates()->where('status', Pedido::DESPACHADO),
            'entregues' => Pedido::withBasicRelations()->withFormattedDates()->where('status', Pedido::ENTREGUE),
            'cancelados' => Pedido::withBasicRelations()->withFormattedDates()->whereIn('status', [
                Pedido::CANCELADO_USUARIO,
                Pedido::CANCELADO_NAO_LOCALIZADO,
                Pedido::CANCELADO_TROTE,
                Pedido::RECUSADO
            ]),
            'agendados' => Pedido::withBasicRelations()->withFormattedDates()
                ->where([
                    ['status', Pedido::PENDENTE],
                    ['agendado', 1]
                ])
                ->whereRaw("DATE(pedido.dataAgendada) > CURDATE() OR (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) > 30)")
        ];

        $this->applyDateFilters($queries, $request);
        $this->applyDistribuidorFilter($queries, $request);
        $this->loadOrderProducts($queries);

        $ultimoPedido = Pedido::orderBy('id', 'DESC')->first();
        $entregadores = Entregador::where("status", Entregador::ATIVO)
            ->select('id', 'nome')
            ->get();

        return [
            $queries['pendentes']->orderBy('horarioPedido', 'desc')->get(),
            $queries['aceitos']->orderBy('horarioPedido', 'desc')->get(),
            $queries['despachados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['entregues']->orderBy('horarioPedido', 'desc')->get(),
            $queries['cancelados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['agendados']->orderBy('horarioPedido', 'desc')->get(),
            $ultimoPedido->id,
            $entregadores
        ];
    }
    /**
 * Applies distributor filters to queries
 * @param array $queries Query collection
 * @param Request $request Filter parameters
 */
private function applyDistribuidorFilter(&$queries, $request)
{
    if ($request->idDistribuidores) {
        $distribuidores = explode(',', $request->idDistribuidores);
        foreach ($queries as $query) {
            $query->whereIn('idDistribuidor', $distribuidores);
        }
    }
}
}
