<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\EnderecoCliente;
use App\Models\Pedido;
use App\Models\Distribuidor;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\Builder;
use \Barryvdh\Debugbar\Facades\Debugbar;
use App\Enums\ReminderStatus;
use App\Models\Reminder;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->tipoAdministrador == "Distribuidor") {
                $distribuidor = Distribuidor::with('enderecoDistribuidor')->selectRaw('idEnderecoDistribuidor')->find(auth()->user()->idDistribuidor);
                $cidade = $distribuidor->enderecoDistribuidor->cidade;
                $clientes = Cliente::select(['id', 'nome', 'tipoPessoa', 'cpf', 'cnpj', 'dddTelefone', 'telefone', 'outrosContatos', 'status', 'email', 'rating'])
                    ->where('status', '!=', Cliente::EXCLUIDO)
                    ->whereHas("enderecos", function (Builder $query) use ($cidade) {
                        $query->where('cidade', 'like', $cidade);
                    })
                    ->with('enderecos');
            } else {
                $clientes = Cliente::select(['id', 'nome', 'tipoPessoa', 'cpf', 'cnpj', 'dddTelefone', 'telefone', 'outrosContatos', 'status', 'email', 'rating', 'sexo', 'dataNascimento'])
                    ->where('status', '!=', Cliente::EXCLUIDO)->with('enderecos');
            }
            return Datatables::of($clientes)
                ->editColumn('tipoPessoa', function (Cliente $cliente) {
                    if ($cliente->tipoPessoa == 1) {
                        return $cliente->cpf;
                    } else {
                        return $cliente->cnpj;
                    }
                })
                ->editColumn('telefone', '({{$dddTelefone}}) {{$telefone}}')
                ->addColumn('rating', function (Cliente $cliente) {
                    if ($cliente->rating > 0) {
                        return '<span class="font-normal inline-block px-2 text-[75%] text-center whitespace-nowrap align-baseline rounded text-white bg-success">' . $cliente->rating . '</span>';
                    } else if ($cliente->rating == 0) {
                        return '<span class="font-normal inline-block px-2 text-[75%] text-center whitespace-nowrap align-baseline rounded text-white bg-inverse">' . $cliente->rating . '</span>';
                    } else if ($cliente->rating < -2) {
                        return '<span class="font-normal inline-block px-2 text-[75%] text-center whitespace-nowrap align-baseline rounded text-white bg-danger">' . $cliente->rating . '</span>';
                    } else {
                        return '<span class="font-normal inline-block px-2 text-[75%] text-center whitespace-nowrap align-baseline rounded text-white bg-warning">' . $cliente->rating . '</span>';
                    }
                })
                ->addColumn('opcoes', function (Cliente $cliente) {
                    $botaoEnviar = auth()->user()->tipoAdministrador == 'Atendente' ? '<button title="EnviarMsg" id=' . $cliente->dddTelefone . $cliente->telefone . ' type="button" class="enviarMsg btn btn-sm btn-circle btn-primary"><i class="fas fa-send"></i></button>' : '';
                    if ($cliente->status == Cliente::ATIVO) {
                        return '<span style="white-space: nowrap;">' . $botaoEnviar .
                            '<button title="Visualizar" id=' . $cliente->id . ' type="button" class="visualizar btn btn-sm btn-circle btn-info"><i class="fas fa-eye"></i></button>' .
                            '<button title="Atualizar" id=' . $cliente->id . ' type="button" class="atualizar btn btn-sm btn-circle btn-secondary"><i class="fas fa-pencil-alt"></i></button>' .
                            '</span><span style="white-space: nowrap;">' .
                            '<button title="Inativar" id=' . $cliente->id . ' type="button" class="inativar btn btn-sm btn-circle btn-warning"><i class="fas fa-pause"></i></button>' .
                            '<button title="Excluir" id=' . $cliente->id . ' type="button" class="excluir btn btn-sm btn-circle btn-danger"><i class="fas fa-trash-alt"></i></button>' .
                            '</span>';
                    } else {
                        return '<span style="white-space: nowrap;">' . $botaoEnviar .
                            '<button title="Visualizar" id=' . $cliente->id . ' type="button" class="visualizar btn btn-sm btn-circle btn-info"><i class="fas fa-eye"></i></button>' .
                            '<button title="Atualizar" id=' . $cliente->id . ' type="button" class="atualizar btn btn-sm btn-circle btn-secondary"><i class="fas fa-pencil-alt"></i></button>' .
                            '</span><span style="white-space: nowrap;">' .
                            '<button title="Ativar" id=' . $cliente->id . ' type="button" class="ativar btn btn-sm btn-circle btn-success"><i class="fas fa-play"></i></button>' .
                            '<button title="Excluir" id=' . $cliente->id . ' type="button" class="excluir btn btn-sm btn-circle btn-danger"><i class="fas fa-trash-alt"></i></button>' .
                            '</span>';
                    }
                })
                ->rawColumns(['rating', 'opcoes'])
                ->make(true);
        } else {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cliente = new Cliente($request->all());
        $cliente->rating = 0;
        $cliente->status = Cliente::ATIVO;
        $telefone = explode(" ", str_replace("-", "", $request->telefone));
        $formatacaoTelefone = array("(", ")");
        $cliente->dddTelefone = str_replace($formatacaoTelefone, "", $telefone[0]);
        $cliente->telefone = $telefone[1];
        $cliente->dataNascimento = $request->dataNascimento == "" ? null : implode("-", array_reverse(explode("/", $request->dataNascimento)));

        //$v = Cliente::get()->where('email', 'like ',$request->email);
        //if ($v->count() == 0 || strcmp($request->email, "") == 0) {
        if ($cliente->save()) {
            $enderecoCliente = new Enderecocliente($request->all());
            $enderecoCliente->referencia = $request->referencia ? $request->referencia : "";
            $enderecoCliente->complemento = $request->complemento ? $request->complemento : "";
            $enderecoCliente->cep = str_replace('-', '', $request->cep);
            $enderecoCliente->idCliente = $cliente->id;
            $coordenadas = $this->buscarLatitudeLongitude($enderecoCliente->logradouro, $enderecoCliente->numero, $enderecoCliente->cidade, $enderecoCliente->estado, $enderecoCliente->cep);
            $enderecoCliente->latitude = $coordenadas[0];
            $enderecoCliente->longitude = $coordenadas[1];
            $enderecoCliente->status = EnderecoCliente::ATIVO;
            if ($enderecoCliente->save()) {
                return $enderecoCliente->id;
                //return response("Cliente cadastrado com sucesso.", 200);
            } else {
                return response("Erro ao cadastrar o cliente. Tente novamente ou contate o cliente.", 400);
            }
        } else {
            return response("Erro ao cadastrar o cliente. Tente novamente ou contate o cliente.", 400);
        }
        // } else {
        //     return response('Email já cadastrado.', 200);
        // }
        //CADASTRA CLIENTE
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show($id)
{
    $cliente = Cliente::with([
        'enderecos' => function($query) {
            $query->select('id', 'idCliente', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'cep', 'complemento', 'referencia')
                  ->where('status', 1);
        },
        'pedidos' => function($query) {
    $query->select(
        'pedido.*' // Select all pedido fields to ensure we have required data
    )
    ->with(['distribuidor' => function($q) {
        $q->select('id', 'nome');  // Include any other distribuidor fields you need
    }])
    ->orderBy('pedido.id', 'desc')
    ->limit(50);
        },
        'reminders' => function($query) {
    $query->select('id', 'id_cliente', 'descricao', 'data_limite', 'status')
          ->where('status', ReminderStatus::ATIVO)
          ->orderBy('data_limite');
}
    ])->select('id', 'nome', 'dddTelefone', 'telefone', 'email', 'dataNascimento', 'rating')
      ->find($id);

    $cliente->dataNascimento = $cliente->dataNascimento ?
        date("d/m/Y", strtotime($cliente->dataNascimento)) : '';

        return response()->json($cliente);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$request->status) {
            $request['cpf'] = preg_replace("/[^0-9]/", "", $request->cpf);
            $request['cnpj'] = preg_replace("/[^0-9]/", "", $request->cnpj);
            $telefone = explode(" ", str_replace("-", "", $request->telefone));
            $formatacaoTelefone = array("(", ")");
            $request['dddTelefone'] = str_replace($formatacaoTelefone, "", $telefone[0]);
            $request['telefone'] = $telefone[1];
            $request['dataNascimento'] = $request->dataNascimento == "" ? null : implode("-", array_reverse(explode("/", $request->dataNascimento)));
        }

        $cliente = Cliente::find($id);
        $cliente->update($request->all());
        return response('Cliente ' . $cliente->nome, 200);
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(cliente $cliente)
    {
        //
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
}
