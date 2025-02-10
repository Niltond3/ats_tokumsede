<?php

namespace App\Actions\Enderecos;

use App\Models\EnderecoCliente;
use App\Models\Cliente;
use App\Models\Distribuidor;
use App\Traits\AddressProcessing;
use Illuminate\Support\Facades\DB;

class ShowEnderecoAction
{
    use AddressProcessing;

    public function execute($id)
    {
        $endereco = EnderecoCliente::find($id);
        $cliente = Cliente::find($id);
        $enderecosCliente = EnderecoCliente::where('idCliente', $id)
            ->where('status', '!=', 3)
            ->get();

        $distribuidores = $this->findNearbyDistributors($enderecosCliente);

        return [$cliente, $enderecosCliente, $endereco, $distribuidores];
    }

    private function findNearbyDistributors($enderecosCliente)
    {
        $fator = 0.2;
        $buscarDistribuidores = true;

        foreach($enderecosCliente as $endereco) {
            $endereco->latitude = doubleval($endereco->latitude);
            $endereco->longitude = doubleval($endereco->longitude);

            $distribuidores = $this->queryNearbyDistributors($endereco, $fator);

            if($distribuidores->isEmpty()) {
                $endereco->distribuidor = "nok";
                continue;
            }

            $dists = $this->calculateDistances($distribuidores, $endereco);
            $indexDistribuidor = $this->selectDist($dists);

            $endereco->distribuidor = $indexDistribuidor == -1 ? "nok" : "ok";
        }

        return $buscarDistribuidores ?
            Distribuidor::where('status', Distribuidor::ATIVO)->get() :
            collect();
    }

    private function queryNearbyDistributors($endereco, $fator)
    {
        return DB::table('distribuidor')
            ->join('enderecoDistribuidor', 'enderecoDistribuidor.id', '=', 'distribuidor.idEnderecoDistribuidor')
            ->select('distribuidor.*',
                'enderecoDistribuidor.latitude',
                'enderecoDistribuidor.longitude',
                'enderecoDistribuidor.distanciaMaxima')
            ->whereRaw("status = ? AND
                enderecoDistribuidor.latitude + ? >= ? AND
                enderecoDistribuidor.latitude - ? <= ? AND
                enderecoDistribuidor.longitude + ? >= ? AND
                enderecoDistribuidor.longitude - ? <= ?",
                [
                    Distribuidor::ATIVO,
                    $fator, $endereco->latitude,
                    $fator, $endereco->latitude,
                    $fator, $endereco->longitude,
                    $fator, $endereco->longitude
                ])
            ->get();
    }

    private function calculateDistances($distribuidores, $endereco)
    {
        $dists = [];
        foreach($distribuidores as $pos2 => $d) {
            $dist = $this->calculateHaversineDistance(
                $d->latitude,
                $d->longitude,
                $endereco->latitude,
                $endereco->longitude
            );

            $dists[$pos2] = [
                "max" => $d->distanciaMaxima,
                "atual" => $dist,
                "pos" => $pos2
            ];
        }
        return $dists;
    }

    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        if(!$lat2 || !$lon2) return PHP_INT_MAX;

        $d2r = 0.017453292519943295769236;
        $dlong = ($lon2 - $lon1) * $d2r;
        $dlat = ($lat2 - $lat1) * $d2r;

        $temp_sin = sin($dlat/2.0);
        $temp_cos = cos($lat1 * $d2r);
        $temp_sin2 = sin($dlong/2.0);

        $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return 6368.1 * $c;
    }
}
