<?php

namespace App\Traits;

trait AddressProcessing
{
    protected function buscarLatitudeLongitude($logradouro, $numero, $cidade, $estado, $cep)
    {
        // $key = "AIzaSyDIt2CSa_K8P64daT3v4Hv8Ml-8IJsFic8";
        $key = "AIzaSyBxP_e0Xopzv9ptFJ_ZKkgjonXgx0TBivE";
        $address = "{$logradouro}, {$numero}, {$cidade}, {$estado}, {$cep}, Brasil";
        $request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address={$address}&sensor=true&key={$key}";

        $xml = simplexml_load_file($request_url);

        if ($xml->status == "OK") {
            return [(string)$xml->result->geometry->location->lat,
                    (string)$xml->result->geometry->location->lng];
        }

        return [null, null];
    }

    protected function selectDist($dists)
    {
        usort($dists, fn($a, $b) => $a['atual'] > $b['atual']);

        foreach($dists as $d){
            if($d["atual"] <= $d["max"]){
                return $d["pos"];
            }
        }

        return -1;
    }
}
