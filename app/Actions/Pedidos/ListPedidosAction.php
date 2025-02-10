<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\Entregador;
use App\Models\Distribuidor;
use Illuminate\Support\Collection;

class ListPedidosAction extends BasePedidoAction
{
    public function execute($user)
    {
        $baseQueryCallback = $this->getBaseQueryCallbackStructure($user);
        $queries = $this->buildQueriesArray($baseQueryCallback, $user);

        $this->loadOrderProducts($queries);

        $ultimoPedido = Pedido::orderBy('id', 'DESC')->first();

        $entregadores = Entregador::where("status", Entregador::ATIVO)
            ->when($user->tipoAdministrador === 'Distribuidor', function($query) use ($user) {
                return $query->where('idDistribuidor', $user->idDistribuidor);
            })
            ->select('id', 'nome')
            ->get();

        return response()->json([
            $queries['pendentes']->orderBy('horarioPedido', 'desc')->get(),
            $queries['aceitos']->orderBy('horarioPedido', 'desc')->get(),
            $queries['despachados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['entregues']->orderBy('horarioPedido', 'desc')->get(),
            $queries['cancelados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['agendados']->orderBy('horarioPedido', 'desc')->get(),
            $ultimoPedido->id,
            $entregadores
        ]);
    }

    private function getBaseQueryCallbackStructure($user) {
        return function() use ($user) {
            $query = Pedido::query()
                ->withBasicRelations()
                ->withCoreFields()
                ->when($user->tipoAdministrador === 'Distribuidor', function($q) use ($user) {
                    $distributor = Distribuidor::find($user->idDistribuidor);

                    if ($distributor->stockUnionsAsMain()->exists()) {
                        $unionIds = $distributor->stockUnionsAsMain()
                            ->pluck('secondary_distributor_id')
                            ->push($distributor->id)
                            ->toArray();

                        return $q->whereIn('pedido.idDistribuidor', $unionIds);
                    }

                    return $q->where('pedido.idDistribuidor', $user->idDistribuidor);
                })
                ->when($user->tipoAdministrador === null, function($q) use ($user) {
                    $q->join('enderecoCliente', 'pedido.idEndereco', 'enderecoCliente.id')
                      ->where('enderecoCliente.idCliente', $user->id);
                });

            return $query;
        };
    }

/**
 * Builds array of queries for different order statuses
 * @param Closure $baseQueryCallback Base query builder
 * @param User $user Authenticated user
 * @return array Status-based queries
 */
private function buildQueriesArray($baseQueryCallback, $user)
{
    // Get union IDs if distributor is main
    $unionDistributorIds = [];
    if ($user->tipoAdministrador === 'Distribuidor') {
        $distributor = Distribuidor::find($user->idDistribuidor);
        if ($distributor->stockUnionsAsMain()->exists()) {
            $unionDistributorIds = $distributor->stockUnionsAsMain()
                ->pluck('secondary_distributor_id')
                ->push($user->idDistribuidor)
                ->toArray();
        }
    }

    $distributorFilter = function($query) use ($user, $unionDistributorIds) {
        if ($user->tipoAdministrador === 'Distribuidor') {
            if (!empty($unionDistributorIds)) {
                $query->whereIn('pedido.idDistribuidor', $unionDistributorIds);
            } else {
                $query->where('pedido.idDistribuidor', $user->idDistribuidor);
            }
        }
    };

    $queries = [
        'pendentes' => $baseQueryCallback()->tap($distributorFilter)
        ->where('pedido.status', Pedido::PENDENTE)
        ->whereRaw("((pedido.agendado = 1 AND (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) <= 30) OR DATE(pedido.dataAgendada) < CURDATE()) OR pedido.agendado = 0)"),

        'aceitos' => $baseQueryCallback()->tap($distributorFilter)
            ->where('pedido.status', Pedido::ACEITO),

        'despachados' => $baseQueryCallback()->tap($distributorFilter)
            ->where('pedido.status', Pedido::DESPACHADO),

        'entregues' => $baseQueryCallback()->tap($distributorFilter)
            ->where('pedido.status', Pedido::ENTREGUE)
            ->whereRaw("DATE(pedido.horarioEntrega) = CURDATE()"),

        'cancelados' => $baseQueryCallback()->tap($distributorFilter)
            ->whereIn('pedido.status', [
                Pedido::CANCELADO_USUARIO,
                Pedido::CANCELADO_NAO_LOCALIZADO,
                Pedido::CANCELADO_TROTE,
                Pedido::RECUSADO
            ])
            ->whereRaw("DATE(pedido.horarioPedido) = CURDATE()"),

        'agendados' => $baseQueryCallback()->tap($distributorFilter)
            ->where([
                ['pedido.status', Pedido::PENDENTE],
                ['agendado', 1]
            ])
            ->whereRaw("DATE(pedido.dataAgendada) > CURDATE() OR (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) > 30)")
    ];

    return $queries;
}
}
