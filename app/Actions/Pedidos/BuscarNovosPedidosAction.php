<?php

namespace App\Actions\Pedidos;

use App\Models\Pedido;
use App\Models\Distribuidor;
use App\Enums\TipoAdministrador;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Collection;

class BuscarNovosPedidosAction extends BasePedidoAction
{
    private const TEMPO_LIMITE_AGENDAMENTO = 30;

    public function execute($ultimoPedidoId): Collection
    {
        $ultimoPedidoId = (int) $ultimoPedidoId;
        $usuario = $this->getAuthenticatedUser();

        return match (TipoAdministrador::from($usuario->tipoAdministrador)) {
            TipoAdministrador::ADMIN => $this->buscarPedidosAdministrador($ultimoPedidoId),
            default => $this->buscarPedidosDistribuidor($ultimoPedidoId, $usuario),
        };
    }

    private function getAuthenticatedUser()
    {
        if (!auth()->check()) {
            throw new AuthenticationException('Usuário não autenticado');
        }
        return auth()->user();
    }

    private function buscarPedidosAdministrador(int $ultimoPedidoId): Collection
    {
        return Pedido::query()
            ->where('pedido.status', Pedido::PENDENTE)
            ->where('id', '>', $ultimoPedidoId)
            ->where(function ($query) {
                $query->where($this->getCriteriosAgendamento())
                    ->orWhere('agendado', false);
            })
            ->with(['distribuidor', 'endereco.clientePedido'])
            ->orderBy('id', 'asc')
            ->get();
    }

    private function buscarPedidosDistribuidor(int $ultimoPedidoId, $usuario): Collection
    {
        $distribuidor = Distribuidor::findOrFail($usuario->idDistribuidor);
        $distribuidorIds = $this->getDistribuidorIds($distribuidor);

        return Pedido::query()
            ->where('id', '>', $ultimoPedidoId)
            ->where('pedido.status', Pedido::PENDENTE)
            ->whereIn('idDistribuidor', $distribuidorIds)
            ->where(function($query) {
                $now = Carbon::now();
                $query->where([
                    ['agendado', '=', true],
                    ['dataAgendada', '>=', $now->format('Y-m-d')]
                ])
                ->orWhere('agendado', false);
            })
            ->with(['endereco.cliente'])
            ->orderBy('id', 'asc')
            ->get();
    }

    private function getCriteriosAgendamento(): \Closure
    {
        return function ($query) {
            $now = Carbon::now();
            $query->where('agendado', true)
                ->where(function ($subquery) use ($now) {
                    $subquery->whereDate('dataAgendada', $now)
                        ->whereRaw(
                            'TIME_TO_SEC(TIMEDIFF(horaInicio, ?)) / 3600 <= ?',
                            [$now->format('H:i:s'), self::TEMPO_LIMITE_AGENDAMENTO / 60]
                        )
                        ->orWhereDate('dataAgendada', '<', $now);
                });
        };
    }

    private function getDistribuidorIds(Distribuidor $distribuidor): array
    {
        if ($distribuidor->stockUnionsAsMain()->exists()) {
            return $distribuidor->stockUnionsAsMain()
                ->pluck('secondary_distributor_id')
                ->push($distribuidor->id)
                ->toArray();
        }
        return [$distribuidor->id];
    }
}
