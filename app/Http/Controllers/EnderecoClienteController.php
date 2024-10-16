<?php

namespace App\Http\Controllers;

use App\Models\EnderecoCliente;
use App\Models\Cliente;
use App\Models\Distribuidor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnderecoClienteController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $enderecoCliente = new Enderecocliente($request->all());
        $enderecoCliente->referencia = $request->referencia?$request->referencia:"";
        $enderecoCliente->complemento = $request->complemento?$request->complemento:"";
        $enderecoCliente->cep = str_replace('-','',$request->cep);
        $coordenadas = $this->buscarLatitudeLongitude($enderecoCliente->logradouro, $enderecoCliente->numero, $enderecoCliente->cidade, $enderecoCliente->estado, $enderecoCliente->cep);
        $enderecoCliente->latitude = $coordenadas[0];
        $enderecoCliente->longitude = $coordenadas[1];
        $enderecoCliente->status = EnderecoCliente::ATIVO;
        if ($enderecoCliente->save()) {
            return $enderecoCliente->id;
        } else {
            return response("Erro ao cadastrar o endereço. Tente novamente ou contate o suporte.", 400);
        }
        //CADASTRA ENDEREÇO CLIENTE
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\enderecoCliente  $enderecoCliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $endereco = EnderecoCliente::find($id);
        $cliente = Cliente::find($id);
        $enderecosCliente = EnderecoCliente::where('idCliente',$id)->where('status','!=',3)->get();
        $fator = doubleval(0.2);
    	$buscarDistribuidores = true;
		for($i = 0; $i < sizeof($enderecosCliente); $i++){

            $enderecosCliente[$i]->latitude = doubleval($enderecosCliente[$i]->latitude);
            $enderecosCliente[$i]->longitude = doubleval($enderecosCliente[$i]->longitude);
            $distribuidores = DB::table('distribuidor')
                ->join('enderecoDistribuidor', 'enderecoDistribuidor.id', '=', 'distribuidor.idEnderecoDistribuidor')
                ->select('distribuidor.*',
                    'enderecoDistribuidor.latitude as latitude',
                    'enderecoDistribuidor.longitude as longitude',
                    'enderecoDistribuidor.distanciaMaxima as distanciaMaxima')
                ->whereRaw("status = ".Distribuidor::ATIVO." AND enderecoDistribuidor.latitude + $fator >= ".$enderecosCliente[$i]->latitude
                        ." AND enderecoDistribuidor.latitude - $fator <= ".$enderecosCliente[$i]->latitude." AND enderecoDistribuidor.longitude + $fator >= "
                        .$enderecosCliente[$i]->longitude." AND enderecoDistribuidor.longitude - $fator <= ".$enderecosCliente[$i]->longitude)
                ->get();

			if(count($distribuidores) == 0){
                $enderecosCliente[$i]->distribuidor = "nok";
                $buscarDistribuidores = true;
			}else{
				$indexDistribuidor = 0; $dists = array();
				foreach($distribuidores as $pos2 => $d){
					if($enderecosCliente[$i]->latitude == NULL || $enderecosCliente[$i]->longitude == NULL){
						$dist = PHP_INT_MAX;
					}else{
                        //$dist = calcDistancia($d["latitude"], $d["longitude"], $enderecosCliente[$i]["latitude"], $enderecosCliente[$i]["longitude"]);
                        $d2r = 0.017453292519943295769236;
                        $dlong = ($enderecosCliente[$i]->longitude - $d->longitude) * $d2r;
                        $dlat = ($enderecosCliente[$i]->latitude - $d->latitude) * $d2r;
                        $temp_sin = sin($dlat/2.0);
                        $temp_cos = cos($d->latitude * $d2r);
                        $temp_sin2 = sin($dlong/2.0);
                        $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
                        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));
                        $dist = 6368.1 * $c;
					}
					$dists[$pos2] = array("max" => $d->distanciaMaxima, "atual" => $dist, "pos" => $pos2);
				}
                $indexDistribuidor = $this->selectDist($dists);
				if($indexDistribuidor == -1){
                    $enderecosCliente[$i]->distribuidor = "nok";
                    $buscarDistribuidores = true;
				}else{
					$enderecosCliente[$i]->distribuidor = "ok";
				}
			}
        }
        if($buscarDistribuidores){
            $distribuidores = Distribuidor::where('status',Distribuidor::ATIVO)->get();
        }
        return [$cliente, $enderecosCliente, $endereco, $distribuidores];
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\enderecoCliente  $enderecoCliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $enderecoCliente = EnderecoCliente::find($id);
        $enderecoCliente->referencia = $request->referencia?$request->referencia:"";
        $enderecoCliente->complemento = $request->complemento?$request->complemento:"";
        $enderecoCliente->cep = $request->cep?$request->cep:"";
        $enderecoCliente->update(array_filter($request->all()));
        return response('O endereço '.$enderecoCliente->id, 200);
        //
    }
    function selectDist($dists){
		usort($dists, function ($a, $b) {
		    return $a['atual'] > $b['atual'];
		});
		$pos = -1;
		foreach($dists as $d){
			if($d["atual"] <= $d["max"]){
				$pos = $d["pos"];
				break;
			}
		}
		return $pos;
    }
    function buscarLatitudeLongitude($logradouro, $numero, $cidade, $estado, $cep) {
        //$address = {nm_bairro}.", ".{nm_cidade}.", ".{nm_estado}.", ".{nm_brasil};
        $key = "AIzaSyDIt2CSa_K8P64daT3v4Hv8Ml-8IJsFic8";
        $address = $logradouro . ", " . $numero . ", " . $cidade . ", " . $estado . ", " . $cep . "," . "Brasil";
        $request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address=" . $address . "&sensor=true&key=".$key; // A URL que vc manda pro google para pegar o XML
        $context = stream_context_create(array('ssl'=>array(
            'verify_peer' => true,
            'cafile' => '/var/www/tokumsede/etc/certificados/ca-bundle.crt'
        )));
        libxml_set_streams_context($context);
        $xml = simplexml_load_file($request_url) or die("url not loading"); // request do XML
        $status = $xml->status; // pega o status do request, já qe a API da google pode retornar vários tipos de respostas
        if ($status == "OK") {
            //request returned completed time to get lat / lang for storage
            $lat = $xml->result->geometry->location->lat;
            $long = $xml->result->geometry->location->lng;
            //echo "$lat,$long";
            $retorno[] = $lat;
            $retorno[] = $long;

            return $retorno;
        }
        if ($status == "ZERO_RESULTS") {
            //indica que o geocode funcionou mas nao retornou resutados.
            //echo "Não Foi possível encontrar o local";
        }
        if ($status == "OVER_QUERY_LIMIT") {
            //indica que sua cota diária de requests excedeu
            //echo "A cota do GoogleMaps excedeu o limite diário";
        }
        if ($status == "REQUEST_DENIED") {
            //indica que seu request foi negado, geralmente por falta de um 'parametro de sensor?'
            //echo "Acesso Negado";
        }
        if ($status == "INVALID_REQUEST") {
            // geralmente indica que a query (address or latlng) está faltando.
            //echo "Endereço não está preenchido corretamente";
        }
    }
}
