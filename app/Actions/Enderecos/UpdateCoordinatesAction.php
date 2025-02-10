<?php

namespace App\Actions\Enderecos;

use App\Models\EnderecoCliente;
use App\Traits\AddressProcessing;
use Illuminate\Http\Request;

class UpdateCoordinatesAction
{
    use AddressProcessing;

    public function execute(Request $request, $id)
    {
        $endereco = EnderecoCliente::findOrFail($id);

        $coordenadas = $this->buscarLatitudeLongitude(
            $endereco->logradouro,
            $endereco->numero,
            $endereco->cidade,
            $endereco->estado,
            $endereco->cep
        );

        $endereco->latitude = $coordenadas[0];
        $endereco->longitude = $coordenadas[1];

        if ($endereco->save()) {
            return response('Coordenadas atualizadas com sucesso', 200);
        }

        return response('Erro ao atualizar coordenadas', 400);
    }
}
