<?php

namespace App\Traits;

trait AddressManagement
{
    protected function handleAddressData($endereco, $request)
    {
        $endereco->referencia = $request->referencia ?? "";
        $endereco->complemento = $request->complemento ?? "";
        $endereco->cep = str_replace('-', '', $request->cep ?? "");

        $coordenadas = $this->buscarLatitudeLongitude(
            $endereco->logradouro,
            $endereco->numero,
            $endereco->cidade,
            $endereco->estado,
            $endereco->cep
        );

        $endereco->latitude = $coordenadas[0];
        $endereco->longitude = $coordenadas[1];

        return $endereco;
    }
}
