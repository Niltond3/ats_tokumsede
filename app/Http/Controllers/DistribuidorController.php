<?php

namespace App\Http\Controllers;

use App\Models\Distribuidor;
use App\Models\EnderecoDistribuidor;
use App\Models\EnderecoCliente;
use App\Models\HorarioFuncionamento;
use App\Models\NovoHorarioFuncionamento;
use App\Models\TaxaEntrega;
use App\Models\Produto;
use App\Models\Estoque;
use Illuminate\Http\Request;
use \Barryvdh\Debugbar\Facades\Debugbar;

class DistribuidorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->tipoAdministrador == "Administrador" || auth()->user()->tipoAdministrador == "Atendente") {
                $distribuidores = Distribuidor::where('status', '!=', 3)->get();
                return $distribuidores;
                //LISTA DISTRIBUIDORES
            } else {
                return response('Você não tem acesso a esta funcionalidade.', 400);
            }
        } else {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\enderecoCliente  $enderecoCliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = auth()->user()->tipoAdministrador == "Distribuidor" ? auth()->user()->idDistribuidor : $id;
        $distribuidor = Distribuidor::find($id)->load('taxaEntrega', 'horarioFuncionamento', 'novoHorarioFuncionamento', 'enderecoDistribuidor');
        $data = array(
            'id' => $distribuidor->id,
            'nome' => $distribuidor->nome,
            'cnpj' => $distribuidor->cnpj,
            'dddTelefone' => $distribuidor->dddTelefone,
            'telefonePrincipal' => $distribuidor->telefonePrincipal,
            'email' => $distribuidor->email,
            'outrosContatos' => $distribuidor->outrosContatos,

            'logradouro' => $distribuidor->enderecoDistribuidor->logradouro,
            'numero' => $distribuidor->enderecoDistribuidor->numero,
            'bairro' => $distribuidor->enderecoDistribuidor->bairro,
            'complemento' => $distribuidor->enderecoDistribuidor->complemento,
            'cep' => $distribuidor->enderecoDistribuidor->cep,
            'cidade' => $distribuidor->enderecoDistribuidor->cidade,
            'estado' => $distribuidor->enderecoDistribuidor->estado,
            'referencia' => $distribuidor->enderecoDistribuidor->referencia,

            'inicioSemana' => $distribuidor->horarioFuncionamento->inicioSemana,
            'fimSemana' => $distribuidor->horarioFuncionamento->fimSemana,
            'inicioSabado' => $distribuidor->horarioFuncionamento->inicioSabado,
            'fimSabado' => $distribuidor->horarioFuncionamento->fimSabado,
            'domingo' => $distribuidor->horarioFuncionamento->domingo,
            'inicioDomingo' => $distribuidor->horarioFuncionamento->inicioDomingo,
            'fimDomingo' => $distribuidor->horarioFuncionamento->fimDomingo,

            'novo_domingo' => $distribuidor->novoHorarioFuncionamento->domingo,
            'novo_inicioDomingo' => $distribuidor->novoHorarioFuncionamento->inicioDomingo,
            'novo_fimDomingo' => $distribuidor->novoHorarioFuncionamento->fimDomingo,
            'segunda' => $distribuidor->novoHorarioFuncionamento->segunda,
            'inicioSegunda' => $distribuidor->novoHorarioFuncionamento->inicioSegunda,
            'fimSegunda' => $distribuidor->novoHorarioFuncionamento->fimSegunda,
            'terca' => $distribuidor->novoHorarioFuncionamento->terca,
            'inicioTerca' => $distribuidor->novoHorarioFuncionamento->inicioTerca,
            'fimTerca' => $distribuidor->novoHorarioFuncionamento->fimTerca,
            'quarta' => $distribuidor->novoHorarioFuncionamento->quarta,
            'inicioQuarta' => $distribuidor->novoHorarioFuncionamento->inicioQuarta,
            'fimQuarta' => $distribuidor->novoHorarioFuncionamento->fimQuarta,
            'quinta' => $distribuidor->novoHorarioFuncionamento->quinta,
            'inicioQuinta' => $distribuidor->novoHorarioFuncionamento->inicioQuinta,
            'fimQuinta' => $distribuidor->novoHorarioFuncionamento->fimQuinta,
            'sexta' => $distribuidor->novoHorarioFuncionamento->sexta,
            'inicioSexta' => $distribuidor->novoHorarioFuncionamento->inicioSexta,
            'fimSexta' => $distribuidor->novoHorarioFuncionamento->fimSexta,
            'novo_sabado' => $distribuidor->novoHorarioFuncionamento->sabado,
            'novo_inicioSabado' => $distribuidor->novoHorarioFuncionamento->inicioSabado,
            'novo_fimSabado' => $distribuidor->novoHorarioFuncionamento->fimSabado,
            'pausaAlmoco' => $distribuidor->novoHorarioFuncionamento->pausaAlmoco,
            'inicioAlmoco' => $distribuidor->novoHorarioFuncionamento->inicioAlmoco,
            'fimAlmoco' => $distribuidor->novoHorarioFuncionamento->fimAlmoco,

            'taxaUnica' => $distribuidor->taxaEntrega->taxaUnica,
            'valorTaxaUnica' => $distribuidor->taxaEntrega->valorTaxaUnica,
            'taxaDomingo' => $distribuidor->taxaEntrega->taxaDomingo,
            'valorTaxaDomingo' => $distribuidor->taxaEntrega->valorTaxaDomingo,
            'taxaCompraMinima' => $distribuidor->taxaEntrega->taxaCompraMinima,
            'valorCompraMinima' => $distribuidor->taxaEntrega->valorCompraMinima,
            'taxaEntregaDistante' => $distribuidor->taxaEntrega->taxaEntregaDistante,
            'distanciaMaxima' => $distribuidor->taxaEntrega->distanciaMaxima,
            'valorKmAdicional' => $distribuidor->taxaEntrega->valorKmAdicional
        );
        return $data;
        //RETORNA DISTRIBUIDOR
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $horarioFuncionamento = new HorarioFuncionamento($request->all());
        $novoHorarioFuncionamento = new NovoHorarioFuncionamento($request->all());
        $novoHorarioFuncionamento->domingo = $request->novo_domingo;
        $novoHorarioFuncionamento->inicioDomingo = $request->novo_inicioDomingo;
        $novoHorarioFuncionamento->fimDomingo = $request->novo_fimDomingo;
        $novoHorarioFuncionamento->inicioSabado = $request->novo_inicioSabado;
        $novoHorarioFuncionamento->fimSabado = $request->novo_fimSabado;
        $enderecoDistribuidor = new EnderecoDistribuidor($request->all());
        $enderecoDistribuidor->distanciaMaxima = null;
        $coordenadas = $this->buscarLatitudeLongitude($enderecoDistribuidor->logradouro, $enderecoDistribuidor->numero, $enderecoDistribuidor->cidade, $enderecoDistribuidor->estado, $enderecoDistribuidor->cep);
        $enderecoDistribuidor->latitude = $coordenadas[0];
        $enderecoDistribuidor->longitude = $coordenadas[1];
        //$enderecoDistribuidor->cidade = $coordenadas[2] == "" ? $enderecoDistribuidor->cidade : $coordenadas[2];
        $taxaEntrega = new TaxaEntrega($request->all());
        $v = Distribuidor::get()->where('cnpj', 'like ', $request->cnpj);
        if ($v->count() == 0 || strcmp($request->cnpj, "") == 0) {
            if ($horarioFuncionamento->save()) {
                if ($novoHorarioFuncionamento->save()) {
                    if ($enderecoDistribuidor->save()) {
                        if ($taxaEntrega->save()) {
                            $distribuidor = new Distribuidor($request->all());
                            $telefone = explode(" ", $request->telefonePrincipal);
                            $formatacaoTelefone = array("(", ")");
                            $distribuidor->dddTelefone = str_replace($formatacaoTelefone, "", $telefone[0]);
                            $distribuidor->telefonePrincipal = $telefone[1];
                            $distribuidor->idEnderecoDistribuidor = $enderecoDistribuidor->id;
                            $distribuidor->idHorarioFuncionamento = $horarioFuncionamento->id;
                            $distribuidor->idNovoHorarioFuncionamento = $novoHorarioFuncionamento->id;
                            $distribuidor->idTaxaEntrega = $taxaEntrega->id;
                            $distribuidor->status = Distribuidor::ATIVO;
                            $distribuidor->tipoDistribuidor = $request->tipoDistribuidor;
                            $distribuidor->idDistribuidor = $request->idDistribuidor;
                            if ($distribuidor->save()) {
                                if ($distribuidor->tipoDistribuidor != "distribuidor") {
                                    $produtos = Produto::get();
                                    foreach ($produtos as $produto) {
                                        $estoque = new Estoque();
                                        $estoque->idDistribuidor = $distribuidor->id;
                                        $estoque->idProduto = $produto["id"];
                                        $estoque->quantidade = 0;
                                        $estoque->save();
                                    }
                                }
                                return response('Distribuidor ' . $distribuidor->nome . ' cadastrado com sucesso.', 200);
                            }
                        }
                    } else {
                        return response('Erro ao cadastrar o distribuidor. Tente novamente ou contate o distribuidor.', 400);
                    }
                }
            } else {
                return response('Erro ao cadastrar o distribuidor. Tente novamente ou contate o distribuidor.', 400);
            }
        } else {
            return response('CNPJ já cadastrado.', 400);
        }
        //CADASTRA DISTRIBUIDOR
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\distribuidor  $distribuidor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->status) {//*Ativa ou Inativa
            $distribuidor = Distribuidor::find($id);
            $distribuidor->status = $request->status;
            $distribuidor->save();
            return response('Distribuidor ' . $distribuidor->nome . ' ativado com sucesso.', 200);
        } else {//*Atualiza cadastro do distribuiodor
            $telefone = explode(" ", $request->telefonePrincipal);
            $formatacaoTelefone = array("(", ")");
            $coordenadas = $this->buscarLatitudeLongitude($request->logradouro, $request->numero, $request->cidade, $request->estado, $request->cep);
            $request->latitude = $coordenadas[0];
            $request->longitude = $coordenadas[1];
            //$request->cidade = $coordenadas[2] == "" ? $request->cidade : $coordenadas[2];
            $distribuidor = Distribuidor::find($id)->load('taxaEntrega', 'horarioFuncionamento', 'novoHorarioFuncionamento', 'enderecoDistribuidor');
            $v = Distribuidor::get()->where('cnpj', 'like', $request->cnpj)->where('id', '!=', $id);
            if ($v->count() == 0 || strcmp($request->cnpj, "") == 0) {
                if ($distribuidor->horarioFuncionamento->update(array_filter($request->all(), 'is_numeric'))) {//salvar novo domingo
                    if (
                        $distribuidor->novoHorarioFuncionamento->update(array_filter($request->all(), function ($value) {
                            return ($value !== false);
                        }))
                    ) {
                        $distribuidor->novoHorarioFuncionamento->domingo = $request->novo_domingo;
                        $distribuidor->novoHorarioFuncionamento->inicioDomingo = $request->novo_inicioDomingo;
                        $distribuidor->novoHorarioFuncionamento->fimDomingo = $request->novo_fimDomingo;
                        $distribuidor->novoHorarioFuncionamento->sabado = $request->novo_sabado;
                        $distribuidor->novoHorarioFuncionamento->inicioSabado = $request->novo_inicioSabado;
                        $distribuidor->novoHorarioFuncionamento->fimSabado = $request->novo_fimSabado;
                        if ($distribuidor->enderecoDistribuidor->update(array_filter($request->except('distanciaMaxima')))) {
                            if ($distribuidor->taxaEntrega->update(array_filter($request->all(), 'is_numeric'))) {
                                if ($request->nome) {
                                    $distribuidor->nome = $request->nome;
                                    $distribuidor->cnpj = $request->cnpj;
                                    $distribuidor->email = $request->email;
                                    $distribuidor->outrosContatos = $request->outrosContatos;
                                    $distribuidor->dddTelefone = str_replace($formatacaoTelefone, "", $telefone[0]);
                                    $distribuidor->telefonePrincipal = $telefone[1];
                                }
                                $distribuidor->novoHorarioFuncionamento->save();
                                $distribuidor->save();
                                return response('Distribuidor atualizado com sucesso.', 200);
                            }
                        } else {
                            return response('Erro ao atualizar o distribuidor. Tente novamente ou contate o distribuidor.', 400);
                        }
                    }
                } else {
                    return response('Erro ao atualizar o distribuidor. Tente novamente ou contate o distribuidor.', 400);
                }
            } else {
                return response('CNPJ já cadastrado.', 400);
            }
        }
        //ATUALIZA DISTRIBUIDOR
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\distribuidor  $distribuidor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $distribuidor = Distribuidor::findOrFail($id);
        $distribuidor->status = Distribuidor::EXCLUIDO;
        $distribuidor->save();
        return $distribuidor->nome;
        //EXCLUI DISTRIBUIDOR
    }
    function buscarLatitudeLongitude($logradouro, $numero, $cidade, $estado, $cep)
    {
        //$address = {nm_bairro}.", ".{nm_cidade}.", ".{nm_estado}.", ".{nm_brasil};
        $key = "AIzaSyDIt2CSa_K8P64daT3v4Hv8Ml-8IJsFic8";
        $address = $logradouro . ", " . $numero . ", " . $cidade . ", " . $estado . ", " . $cep . "," . "Brasil";
        $request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address=" . $address . "&sensor=true&key=" . $key; // A URL que vc manda pro google para pegar o XML

        // $context = stream_context_create(array('ssl'=>array(
        //     'verify_peer' => true,
        //     'cafile' => '/var/www/tokumsede/etc/certificados/ca-bundle.crt'
        // )));
        // libxml_set_streams_context($context);

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

    public function getAllDistributors()
    {
        $distribuidores = Distribuidor::with([
            'enderecoDistribuidor',
            'horarioFuncionamento',
            'novoHorarioFuncionamento',
            'taxaEntrega'
        ])->where('status', Distribuidor::ATIVO)->get();
        $distribuidoresArray = array();

        foreach ($distribuidores as $distribuidor) {
            $distribuidoresArray[] = array(
                'id' => $distribuidor->id,
                'nome' => $distribuidor->nome,
                'cnpj' => $distribuidor->cnpj,
                'dddTelefone' => $distribuidor->dddTelefone,
                'telefonePrincipal' => $distribuidor->telefonePrincipal,
                'email' => $distribuidor->email,
                'outrosContatos' => $distribuidor->outrosContatos,
                'logradouro' => $distribuidor->enderecoDistribuidor->logradouro,
                'numero' => $distribuidor->enderecoDistribuidor->numero,
                'bairro' => $distribuidor->enderecoDistribuidor->bairro,
                'complemento' => $distribuidor->enderecoDistribuidor->complemento,
                'cep' => $distribuidor->enderecoDistribuidor->cep,
                'cidade' => $distribuidor->enderecoDistribuidor->cidade,
                'estado' => $distribuidor->enderecoDistribuidor->estado,
                'referencia' => $distribuidor->enderecoDistribuidor->referencia,
                'inicioSemana' => $distribuidor->horarioFuncionamento->inicioSemana,
                'fimSemana' => $distribuidor->horarioFuncionamento->fimSemana,
                'inicioSabado' => $distribuidor->horarioFuncionamento->inicioSabado,
                'fimSabado' => $distribuidor->horarioFuncionamento->fimSabado,
                'domingo' => $distribuidor->horarioFuncionamento->domingo,
                'inicioDomingo' => $distribuidor->horarioFuncionamento->inicioDomingo,
                'fimDomingo' => $distribuidor->horarioFuncionamento->fimDomingo,
                'novo_domingo' => $distribuidor->novoHorarioFuncionamento->domingo,
                'novo_inicioDomingo' => $distribuidor->novoHorarioFuncionamento->inicioDomingo,
                'novo_fimDomingo' => $distribuidor->novoHorarioFuncionamento->fimDomingo,
                'segunda' => $distribuidor->novoHorarioFuncionamento->segunda,
                'inicioSegunda' => $distribuidor->novoHorarioFuncionamento->inicioSegunda,
                'fimSegunda' => $distribuidor->novoHorarioFuncionamento->fimSegunda,
                'terca' => $distribuidor->novoHorarioFuncionamento->terca,
                'inicioTerca' => $distribuidor->novoHorarioFuncionamento->inicioTerca,
                'fimTerca' => $distribuidor->novoHorarioFuncionamento->fimTerca,
                'quarta' => $distribuidor->novoHorarioFuncionamento->quarta,
                'inicioQuarta' => $distribuidor->novoHorarioFuncionamento->inicioQuarta,
                'fimQuarta' => $distribuidor->novoHorarioFuncionamento->fimQuarta,
                'quinta' => $distribuidor->novoHorarioFuncionamento->quinta,
                'inicioQuinta' => $distribuidor->novoHorarioFuncionamento->inicioQuinta,
                'fimQuinta' => $distribuidor->novoHorarioFuncionamento->fimQuinta,
                'sexta' => $distribuidor->novoHorarioFuncionamento->sexta,
                'inicioSexta' => $distribuidor->novoHorarioFuncionamento->inicioSexta,
                'fimSexta' => $distribuidor->novoHorarioFuncionamento->fimSexta,
                'novo_sabado' => $distribuidor->novoHorarioFuncionamento->sabado,
                'novo_inicioSabado' => $distribuidor->novoHorarioFuncionamento->inicioSabado,
                'novo_fimSabado' => $distribuidor->novoHorarioFuncionamento->fimSabado,
                'pausaAlmoco' => $distribuidor->novoHorarioFuncionamento->pausaAlmoco,
                'inicioAlmoco' => $distribuidor->novoHorarioFuncionamento->inicioAlmoco,
                'fimAlmoco' => $distribuidor->novoHorarioFuncionamento->fimAlmoco,
                'taxaUnica' => $distribuidor->taxaEntrega->taxaUnica,
                'valorTaxaUnica' => $distribuidor->taxaEntrega->valorTaxaUnica,
                'taxaDomingo' => $distribuidor->taxaEntrega->taxaDomingo,
                'valorTaxaDomingo' => $distribuidor->taxaEntrega->valorTaxaDomingo,
                'taxaCompraMinima' => $distribuidor->taxaEntrega->taxaCompraMinima,
                'valorCompraMinima' => $distribuidor->taxaEntrega->valorCompraMinima,
                'taxaEntregaDistante' => $distribuidor->taxaEntrega->taxaEntregaDistante,
                'distanciaMaxima' => $distribuidor->taxaEntrega->distanciaMaxima,
                'valorKmAdicional' => $distribuidor->taxaEntrega->valorKmAdicional
            );
        }
        return response()->json([
            'data' => $distribuidoresArray,
            'meta' => [
                'total' => count($distribuidoresArray)
            ]
        ]);
    }

    function findDistributorByAddress($addressId)
    {
        debugbar::info($addressId);

        $distributor = Distribuidor::with(['enderecoDistribuidor', 'taxaEntrega'])
            ->where('idEnderecoDistribuidor', $addressId)
            ->where('status', Distribuidor::ATIVO)
            ->first();

        if (!$distributor) {
            return response()->json([
                'message' => 'No distributor found for this address'
            ], 404);
        }

        return response()->json([
            'data' => [
                'id' => $distributor->id,
                'nome' => $distributor->nome,
                'endereco' => [
                    'logradouro' => $distributor->enderecoDistribuidor->logradouro,
                    'bairro' => $distributor->enderecoDistribuidor->bairro,
                    'cidade' => $distributor->enderecoDistribuidor->cidade,
                    'estado' => $distributor->enderecoDistribuidor->estado,
                    'distanciaMaxima' => $distributor->enderecoDistribuidor->distanciaMaxima
                ],
                'taxaEntrega' => $distributor->taxaEntrega
            ]
        ]);
    }

    public function findDistributorByClientAddress($clientAddressId)
{
    $clientAddress = EnderecoCliente::find($clientAddressId);

    if (!$clientAddress) {
        return response()->json(['message' => 'Client address not found'], 404);
    }

    $fator = 0.5; // Search radius factor

    $distribuidor = Distribuidor::with(['enderecoDistribuidor', 'taxaEntrega'])
        ->whereHas('enderecoDistribuidor', function($query) use ($clientAddress, $fator) {
            $query->whereRaw("latitude + $fator >= ".$clientAddress->latitude
                ." AND latitude - $fator <= ".$clientAddress->latitude
                ." AND longitude + $fator >= ".$clientAddress->longitude
                ." AND longitude - $fator <= ".$clientAddress->longitude);
        })
        ->where('status', Distribuidor::ATIVO)
        ->get()->map(function($distribuidor) use ($clientAddress) {
            $d2r = 0.017453292519943295769236;
            $dlong = ($clientAddress->longitude - $distribuidor->enderecoDistribuidor->longitude) * $d2r;
            $dlat = ($clientAddress->latitude - $distribuidor->enderecoDistribuidor->latitude) * $d2r;

            $temp_sin = sin($dlat/2.0);
            $temp_cos = cos($distribuidor->enderecoDistribuidor->latitude * $d2r);
            $temp_sin2 = sin($dlong/2.0);

            $a = ($temp_sin * $temp_sin) + ($temp_cos * cos($clientAddress->latitude * $d2r) * $temp_sin2 * $temp_sin2);
            $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

            $distribuidor->distance = 6368.1 * $c;

            return $distribuidor;
        })
        ->sortBy('distance')
        ->first();

        if (!$distribuidor) {
            return response()->json(['message' => 'No distributors found in this area'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $distribuidor->id,
                'nome' => $distribuidor->nome,
                'endereco' => [
                    'logradouro' => $distribuidor->enderecoDistribuidor->logradouro,
                    'bairro' => $distribuidor->enderecoDistribuidor->bairro,
                    'cidade' => $distribuidor->enderecoDistribuidor->cidade,
                    'estado' => $distribuidor->enderecoDistribuidor->estado,
                    'distanciaMaxima' => $distribuidor->enderecoDistribuidor->distanciaMaxima
                ],
                'taxaEntrega' => $distribuidor->taxaEntrega,
                'distancia' => $distribuidor->distance
            ]
        ]);
}
};
