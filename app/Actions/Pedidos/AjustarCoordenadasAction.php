<?php

namespace App\Actions\Pedidos;

use App\Models\EnderecoCliente;
use App\Models\Distribuidor;
use App\Models\Administrador;
use Illuminate\Http\Request;

class AjustarCoordenadasAction extends BasePedidoAction
{
    public function execute(Request $request, $idEndereco)
    {
        $enderecoCliente = EnderecoCliente::find($idEndereco);
        $user = Administrador::find(auth()->user()->id);

        if ($user->tipoAdministrador == "Entregador") {
            return "Erro ao processar coordenadas.";
        }

        $distribuidor = Distribuidor::with("enderecoDistribuidor")
            ->where("id", $request->idDistribuidor ?: $user->idDistribuidor)
            ->first();

        $enderecoCliente->latitude = $distribuidor->enderecoDistribuidor->latitude;
        $enderecoCliente->longitude = $distribuidor->enderecoDistribuidor->longitude;

        if ($enderecoCliente->save()) {
            return "Coordenadas ajustadas com sucesso.";
        }

        return "Erro ao processar coordenadas.";
    }
}
