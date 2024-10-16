<?php

namespace App\Http\Controllers\Cliente\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Cliente/Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:50',
            'dddTelefone' => 'required|string|max:2',
            'telefone' => 'required|string|max:9|unique:'.Cliente::class,
            'sexo' => 'nullable|int',
            'dataNascimento' => 'nullable|date',
            'email' => 'required|string|lowercase|email|max:255|:',
            'senha' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipoPessoa' => 'required|string',
            'cpf' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'outrosContatos' => 'nullable|string',
        ]);

        // $cliente = Cliente::create([
        //     'nome' => $request->nome,
        //     'email' => $request->email,
        //     'senha' => Hash::make($request->senha),
        //     'dddTelefone' => $request->dddTelefone,
        //     'telefone' => $request->telefone,
        //     'sexo' => $request->sexo,
        //     'dataNascimento' => $request->dataNascimento,
        //     'tipoPessoa' => $request->tipoPessoa,
        //     'cpf' => $request->cpf,
        //     'cnpj' => $request->cnpj,
        //     'outrosContatos' => $request->outrosContatos,
        // ]);

        $cliente = new Cliente($request->all());
        $cliente->rating = 0;
        $cliente->status = Cliente::ATIVO;
        $telefone = explode(" ", str_replace("-", "", $request->telefone));
        $formatacaoTelefone = array("(", ")");
        $cliente->dddTelefone = str_replace($formatacaoTelefone, "", $telefone[0]);
        $cliente->telefone = $telefone[1];
        $cliente->dataNascimento = $request->dataNascimento == "" ? null : implode("-", array_reverse(explode("/", $request->dataNascimento)));
        // event(new Registered($cliente));
        $v = Cliente::get()->where('email', 'like ',$request->email);
        if ($v->count() == 0 || strcmp($request->email, "") == 0) {
            if ($cliente->save()) {
                $enderecoCliente = new Enderecocliente($request->all());
                $enderecoCliente->referencia = $request->referencia?$request->referencia:"";
                $enderecoCliente->complemento = $request->complemento?$request->complemento:"";
                $enderecoCliente->cep = str_replace('-','',$request->cep);
                $enderecoCliente->idCliente = $cliente->id;
                $coordenadas = $this->buscarLatitudeLongitude($enderecoCliente->logradouro, $enderecoCliente->numero, $enderecoCliente->cidade, $enderecoCliente->estado, $enderecoCliente->cep);
                $enderecoCliente->latitude = $coordenadas[0];
                $enderecoCliente->longitude = $coordenadas[1];
                $enderecoCliente->status = EnderecoCliente::ATIVO;
                if ($enderecoCliente->save()) {
                    Auth::guard('cliente')->login($cliente);
                    // return $enderecoCliente->id;
                    // return response("Cliente cadastrado com sucesso.", 200);
                    return redirect(route('cliente.dashboard', absolute: false));
                } else {
                    return response("Erro ao cadastrar o cliente. Tente novamente ou contate o cliente.", 400);
                }
            } else {
                return response("Erro ao cadastrar o cliente. Tente novamente ou contate o cliente.", 400);
            }
        } else {
            return response('Email já cadastrado.', 200);
        }
        //CADASTRA CLIENTE
    }

    function buscarLatitudeLongitude($logradouro, $numero, $cidade, $estado, $cep) {
        //$address = {nm_bairro}.", ".{nm_cidade}.", ".{nm_estado}.", ".{nm_brasil};
        $key = "AIzaSyD3A65oIloNfr-TA3EK8vERo2nnWEi1fxg";
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
