<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\api\UtilController;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\EnderecoCliente;
use App\Models\Preco;
use App\Models\Feriado;
use App\Models\Administrador;
use App\Models\ClientePotencial;
use App\Models\Distribuidor;
use App\Models\Composicao;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Mailgun\Mailgun;
use DateTimeZone;
use DateTime;

class IndexController extends Controller
{
       /**
     * Initial Method.
     * @return
     *
     */
    function index(){
	}

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
    }
    // ---------------------------------- Refatorados ------------------------------------------------//
	function verificaPedidoAlterado(){

		$idCliente = $this->escape("idCliente");

        $cliente = Cliente::find($idCliente);

		if($cliente){
            $pedidos = Pedido::where('statusChange', 1)
            ->whereHas('enderecoCliente.cliente', function($query) use ($cliente) {
                $query->where('id', $cliente->id);
            })
            ->with(['enderecoCliente', 'enderecoCliente.cliente']) // Se você precisar carregar os relacionamentos
            ->get();


			if(count($pedidos)){
				foreach($pedidos as $p){
					$this->gcmSend($cliente->regId, $idCliente, $p["id"], $p["status"], $p["retorno"], $p["origem"], false);
				}
			}
		}

	}

    function verificaEmail() {

        $email = $this->escape("email");

        $users = Cliente::where('email', $email)
        ->where('status', Cliente::ATIVO)
        ->get();

		$out["qtd"] = count($users);
		echo json_encode($out);

    }

    function consultaInicial($clie = null){

		$idCliente 	= ($clie == null) ? $this->escape("idCliente") : $clie;
		$system 	= $this->escape("system");
		$cliente = Cliente::find($idCliente);

		$appVersion	= $this->escape("appVersion")?$this->escape("appVersion"):$cliente->appVersion;

		$distrib = Distribuidor::with([
            'enderecoDistribuidor:id,cidade,logradouro,estado,bairro,complemento,numero',
            'novoHorarioFuncionamento:id,segunda,inicioSegunda,fimSegunda,terca,inicioTerca,fimTerca,quarta,inicioQuarta,fimQuarta,quinta,inicioQuinta,fimQuinta,sexta,inicioSexta,fimSexta,sabado,inicioSabado,fimSabado,domingo,inicioDomingo,fimDomingo,pausaAlmoco,inicioAlmoco,fimAlmoco'
        ])
        ->where('status', Distribuidor::ATIVO)
        ->get();

		foreach($distrib as $pos => $d){
			$out["distrib"][$pos]["nome"] 			= $d["nome"];
			$out["distrib"][$pos]["contato"] 		= "(".$d["dddTelefone"].") ".$d["telefonePrincipal"];
			$out["distrib"][$pos]["email"] 			= $d["email"];
			$out["distrib"][$pos]["logradouro"] 	= $d["logradouro"];
			$out["distrib"][$pos]["numero"] 		= $d["numero"];
			$out["distrib"][$pos]["complemento"]	= $d["complemento"];
			$out["distrib"][$pos]["bairro"] 		= $d["bairro"];
			$out["distrib"][$pos]["cidade"] 		= $d["cidade"];
			$out["distrib"][$pos]["estado"] 		= $d["estado"];

			$out["distrib"][$pos]["horario"][0]["funciona"] = $d["domingo"];
			$out["distrib"][$pos]["horario"][0]["inicio"] 	= $d["inicioDomingo"];
			$out["distrib"][$pos]["horario"][0]["fim"] 		= $d["fimDomingo"];
			$out["distrib"][$pos]["horario"][1]["funciona"] = $d["segunda"];
			$out["distrib"][$pos]["horario"][1]["inicio"] 	= $d["inicioSegunda"];
			$out["distrib"][$pos]["horario"][1]["fim"] 		= $d["fimSegunda"];
			$out["distrib"][$pos]["horario"][2]["funciona"] = $d["terca"];
			$out["distrib"][$pos]["horario"][2]["inicio"] 	= $d["inicioTerca"];
			$out["distrib"][$pos]["horario"][2]["fim"] 		= $d["fimTerca"];
			$out["distrib"][$pos]["horario"][3]["funciona"] = $d["quarta"];
			$out["distrib"][$pos]["horario"][3]["inicio"] 	= $d["inicioQuarta"];
			$out["distrib"][$pos]["horario"][3]["fim"] 		= $d["fimQuarta"];
			$out["distrib"][$pos]["horario"][4]["funciona"] = $d["quinta"];
			$out["distrib"][$pos]["horario"][4]["inicio"] 	= $d["inicioQuinta"];
			$out["distrib"][$pos]["horario"][4]["fim"] 		= $d["fimQuinta"];
			$out["distrib"][$pos]["horario"][5]["funciona"] = $d["sexta"];
			$out["distrib"][$pos]["horario"][5]["inicio"] 	= $d["inicioSexta"];
			$out["distrib"][$pos]["horario"][5]["fim"] 		= $d["fimSexta"];
			$out["distrib"][$pos]["horario"][6]["funciona"] = $d["sabado"];
			$out["distrib"][$pos]["horario"][6]["inicio"] 	= $d["inicioSabado"];
			$out["distrib"][$pos]["horario"][6]["fim"] 		= $d["fimSabado"];
			$out["distrib"][$pos]["pausaAlmoco"] 			= $d["pausaAlmoco"];
			$out["distrib"][$pos]["inicioAlmoco"] 			= $d["inicioAlmoco"];
			$out["distrib"][$pos]["fimAlmoco"]		 		= $d["fimAlmoco"];
		}


		if($cliente && $cliente->status == Cliente::ATIVO){

			if($system != null){
				$cliente->appVersion = $system." => ".$appVersion;
			}
			$cliente->ultimoLogin = date('Y-m-d H:i:s');
			$cliente->save();

			$out["msg"] = "ok";
			$enderecos = EnderecoCliente::where('idCliente', $idCliente)
    ->where('status', EnderecoCliente::ATIVO)
    ->get()
    ->toArray();

			$out["freeTax"] = array(1, 4, 5, 6); //ID dos produtos que geram liberação de Taxa de Entrega
			$fator = 0.2; //Raio de aproximadamente 30km

			foreach($enderecos as $pos => $endereco){
				$out["enderecos"][$pos]["id"] 			= $endereco["id"];
				$out["enderecos"][$pos]["apelido"] 		= $endereco["apelido"];
				$out["enderecos"][$pos]["atual"] 		= (bool)$endereco["atual"];
				$out["enderecos"][$pos]["logradouro"] 	= $endereco["logradouro"];
				$out["enderecos"][$pos]["numero"] 		= $endereco["numero"];
				$out["enderecos"][$pos]["bairro"] 		= $endereco["bairro"];
				$out["enderecos"][$pos]["complemento"] 	= $endereco["complemento"];
				$out["enderecos"][$pos]["cep"] 			= $endereco["cep"];
				$out["enderecos"][$pos]["cidade"] 		= $endereco["cidade"];
				$out["enderecos"][$pos]["estado"] 		= $endereco["estado"];
				$out["enderecos"][$pos]["referencia"] 	= $endereco["referencia"];
				$out["enderecos"][$pos]["latitude"] 	= $endereco["latitude"];
				$out["enderecos"][$pos]["longitude"] 	= $endereco["longitude"];

				if($appVersion == null){
					$select = [
                        'h.inicioSemana as inicioSemana',
                        'h.fimSemana as fimSemana',
                        'h.inicioSabado as inicioSabado',
                        'h.fimSabado as fimSabado',
                        'h.domingo as domingo',
                        'h.inicioDomingo as inicioDomingo',
                        'h.fimDomingo as fimDomingo'
                    ];
                    $joinHorario = 'd.Horariofuncionamento h';
				}else{
					$select = [
                        'h.domingo as domingo',
                        'h.inicioDomingo as inicioDomingo',
                        'h.fimDomingo as fimDomingo',
                        'h.segunda as segunda',
                        'h.inicioSegunda as inicioSegunda',
                        'h.fimSegunda as fimSegunda',
                        'h.terca as terca',
                        'h.inicioTerca as inicioTerca',
                        'h.fimTerca as fimTerca',
                        'h.quarta as quarta',
                        'h.inicioQuarta as inicioQuarta',
                        'h.fimQuarta as fimQuarta',
                        'h.quinta as quinta',
                        'h.inicioQuinta as inicioQuinta',
                        'h.fimQuinta as fimQuinta',
                        'h.sexta as sexta',
                        'h.inicioSexta as inicioSexta',
                        'h.fimSexta as fimSexta',
                        'h.sabado as sabado',
                        'h.inicioSabado as inicioSabado',
                        'h.fimSabado as fimSabado',
                        'h.pausaAlmoco as pausaAlmoco',
                        'h.inicioAlmoco as inicioAlmoco',
                        'h.fimAlmoco as fimAlmoco'
                    ];
                    $joinHorario = 'd.Novohorariofuncionamento h';
				}

				// Recuperando os distribuidores com Eloquent
                $distribuidores = Distribuidor::with([
                    'enderecoDistribuidor:id,cidade,latitude,longitude,cep,distanciaMaxima',
                    'taxaEntrega:id,taxaUnica,valorTaxaUnica,taxaDomingo,valorTaxaDomingo,taxaCompraMinima,valorCompraMinima,taxaEntregaDistante,distanciaMaxima,valorKmAdicional',
                    'horarioFuncionamento' => function($query) use ($select) {
                        $query->select($select); // Condicionalmente inclui os campos de horário
                    }
                ])
                ->where('d.status', Distribuidor::ATIVO)
                ->whereRaw('ed.latitude + ? >= ? AND ed.latitude - ? <= ?', [$fator, $endereco["latitude"], $fator, $endereco["latitude"]])
                ->whereRaw('ed.longitude + ? >= ? AND ed.longitude - ? <= ?', [$fator, $endereco["longitude"], $fator, $endereco["longitude"]])
                ->get()
                ->toArray();

				if(count($distribuidores) == 0){
					$out["distribuidores"][$pos]["idDistribuidor"] = null;
				}else{
					//DETERMINA DISTRIBUIDOR MAIS PRÓXIMO
					$indexDistribuidor = 0; $dists = array();
					foreach($distribuidores as $pos2 => $d){
						$dist = calcDistancia($d["latitude"], $d["longitude"], $endereco["latitude"], $endereco["longitude"]);
						$dists[$pos2] = array("max" => $d["distanciaMaxima"], "atual" => $dist, "pos" => $pos2);
					}
					$indexDistribuidor = $this->selectDist($dists);

					if($indexDistribuidor != -1){ //CASO O RAIO DE ATENDIMENTO DO DISTRIBUIDOR ALCANCE A LOCALIZACAO DO CLIENTE

						if($distribuidores[$indexDistribuidor]["tipoDistribuidor"]=="revendedor"){
							$produtos = Preco::with([
                                'produto:id,nome,descricao,img',
                                'produto.categoria:id,nome',
                                'estoque:id,quantidade'
                            ])
                            ->where('pre.status', Preco::ATIVO)
                            ->where('pre.idDistribuidor', $distribuidores[$indexDistribuidor]["idDistribuidor"])
                            ->where('e.quantidade', '>=', 1)
                            ->where(function($query) {
                                $query->whereNull('pre.inicioValidade')
                                      ->orWhere('pre.inicioValidade', '<=', now());
                            })
                            ->where(function($query) {
                                $query->whereNull('pre.fimValidade')
                                      ->orWhere('pre.fimValidade', '>=', now());
                            })
                            ->where(function($query) {
                                $query->whereNull('pre.inicioHora')
                                      ->orWhere('pre.inicioHora', '<=', now()->format('H:i:s'));
                            })
                            ->where(function($query) {
                                $query->whereNull('pre.fimHora')
                                      ->orWhere('pre.fimHora', '>', now()->format('H:i:s'));
                            })
                            ->whereNull('pre.idCliente')
                            ->orderBy('cat.nome', 'ASC')
                            ->orderBy('pro.nome')
                            ->orderBy('pre.qtdMin', 'ASC')
                            ->get()
							->toArray();
						}else{
							    $produtos = Preco::with([
                                    'produto:id,nome,descricao,img',
                                    'produto.categoria:id,nome',
                                    'estoque:id,quantidade'
                                ])
                                ->where('pre.status', Preco::ATIVO)
                                ->where('pre.idDistribuidor', $distribuidores[$indexDistribuidor]["id"])
                                ->where('e.quantidade', '>=', 1)
                                ->where(function($query) {
                                    // Filtro para validade de data
                                    $query->whereNull('pre.inicioValidade')
                                          ->orWhere('pre.inicioValidade', '<=', now());
                                })
                                ->where(function($query) {
                                    $query->whereNull('pre.fimValidade')
                                          ->orWhere('pre.fimValidade', '>=', now());
                                })
                                ->where(function($query) {
                                    $query->whereNull('pre.inicioHora')
                                          ->orWhere('pre.inicioHora', '<=', now()->format('H:i:s'));
                                })
                                ->where(function($query) {
                                    $query->whereNull('pre.fimHora')
                                          ->orWhere('pre.fimHora', '>', now()->format('H:i:s'));
                                })
                                ->whereNull('pre.idCliente')
                                ->orderBy('cat.nome', 'ASC')
                                ->orderBy('pro.nome', 'ASC')
                                ->orderBy('pre.qtdMin', 'ASC')
                                ->get()
								->toArray();
						}
						if(count($produtos)){
							//MONTA JSON DE PRODUTOS
							$out["distribuidores"][$pos]["idDistribuidor"] = $distribuidores[$indexDistribuidor]["id"];
							$out["distribuidores"][$pos]["contato"] = "(".$distribuidores[$indexDistribuidor]["dddTelefone"].") ".$distribuidores[$indexDistribuidor]["telefonePrincipal"];
							if($appVersion == null){
								$out["distribuidores"][$pos]["inicioSemana"] 	= $distribuidores[$indexDistribuidor]["inicioSemana"];
								$out["distribuidores"][$pos]["fimSemana"] 		= $distribuidores[$indexDistribuidor]["fimSemana"];
								$out["distribuidores"][$pos]["inicioSabado"] 	= $distribuidores[$indexDistribuidor]["inicioSabado"];
								$out["distribuidores"][$pos]["fimSabado"] 		= $distribuidores[$indexDistribuidor]["fimSabado"];
								$out["distribuidores"][$pos]["domingo"] 		= (bool)$distribuidores[$indexDistribuidor]["domingo"];
								$out["distribuidores"][$pos]["inicioDomingo"] 	= $distribuidores[$indexDistribuidor]["inicioDomingo"];
								$out["distribuidores"][$pos]["fimDomingo"] 		= $distribuidores[$indexDistribuidor]["fimDomingo"];
							}else{
								$out["distribuidores"][$pos]["horario"][0]["funciona"] 	= $distribuidores[$indexDistribuidor]["domingo"];
								$out["distribuidores"][$pos]["horario"][0]["inicio"] 	= $distribuidores[$indexDistribuidor]["inicioDomingo"];
								$out["distribuidores"][$pos]["horario"][0]["fim"] 		= $distribuidores[$indexDistribuidor]["fimDomingo"];
								$out["distribuidores"][$pos]["horario"][1]["funciona"] 	= $distribuidores[$indexDistribuidor]["segunda"];
								$out["distribuidores"][$pos]["horario"][1]["inicio"] 	= $distribuidores[$indexDistribuidor]["inicioSegunda"];
								$out["distribuidores"][$pos]["horario"][1]["fim"] 		= $distribuidores[$indexDistribuidor]["fimSegunda"];
								$out["distribuidores"][$pos]["horario"][2]["funciona"] 	= $distribuidores[$indexDistribuidor]["terca"];
								$out["distribuidores"][$pos]["horario"][2]["inicio"] 	= $distribuidores[$indexDistribuidor]["inicioTerca"];
								$out["distribuidores"][$pos]["horario"][2]["fim"] 		= $distribuidores[$indexDistribuidor]["fimTerca"];
								$out["distribuidores"][$pos]["horario"][3]["funciona"] 	= $distribuidores[$indexDistribuidor]["quarta"];
								$out["distribuidores"][$pos]["horario"][3]["inicio"] 	= $distribuidores[$indexDistribuidor]["inicioQuarta"];
								$out["distribuidores"][$pos]["horario"][3]["fim"] 		= $distribuidores[$indexDistribuidor]["fimQuarta"];
								$out["distribuidores"][$pos]["horario"][4]["funciona"] 	= $distribuidores[$indexDistribuidor]["quinta"];
								$out["distribuidores"][$pos]["horario"][4]["inicio"] 	= $distribuidores[$indexDistribuidor]["inicioQuinta"];
								$out["distribuidores"][$pos]["horario"][4]["fim"] 		= $distribuidores[$indexDistribuidor]["fimQuinta"];
								$out["distribuidores"][$pos]["horario"][5]["funciona"] 	= $distribuidores[$indexDistribuidor]["sexta"];
								$out["distribuidores"][$pos]["horario"][5]["inicio"] 	= $distribuidores[$indexDistribuidor]["inicioSexta"];
								$out["distribuidores"][$pos]["horario"][5]["fim"] 		= $distribuidores[$indexDistribuidor]["fimSexta"];
								$out["distribuidores"][$pos]["horario"][6]["funciona"] 	= $distribuidores[$indexDistribuidor]["sabado"];
								$out["distribuidores"][$pos]["horario"][6]["inicio"] 	= $distribuidores[$indexDistribuidor]["inicioSabado"];
								$out["distribuidores"][$pos]["horario"][6]["fim"] 		= $distribuidores[$indexDistribuidor]["fimSabado"];
								$out["distribuidores"][$pos]["pausaAlmoco"] 			= $distribuidores[$indexDistribuidor]["pausaAlmoco"];
								$out["distribuidores"][$pos]["inicioAlmoco"] 			= $distribuidores[$indexDistribuidor]["inicioAlmoco"];
								$out["distribuidores"][$pos]["fimAlmoco"]		 		= $distribuidores[$indexDistribuidor]["fimAlmoco"];
							}

							$out["distribuidores"][$pos]["distancia"] = calcDistancia($distribuidores[$indexDistribuidor]["latitude"], $distribuidores[$indexDistribuidor]["longitude"], $endereco["latitude"], $endereco["longitude"]);;
							$out["distribuidores"][$pos]["taxaUnica"] = $distribuidores[$indexDistribuidor]["taxaUnica"];
							$out["distribuidores"][$pos]["valorTaxaUnica"] = $distribuidores[$indexDistribuidor]["valorTaxaUnica"];
							$out["distribuidores"][$pos]["taxaDomingo"] = $distribuidores[$indexDistribuidor]["taxaDomingo"];
							$out["distribuidores"][$pos]["valorTaxaDomingo"] = $distribuidores[$indexDistribuidor]["valorTaxaDomingo"];
							$out["distribuidores"][$pos]["taxaCompraMinima"] = $distribuidores[$indexDistribuidor]["taxaCompraMinima"];
							$out["distribuidores"][$pos]["valorCompraMinima"] = $distribuidores[$indexDistribuidor]["valorCompraMinima"];
							$out["distribuidores"][$pos]["taxaEntregaDistante"] = $distribuidores[$indexDistribuidor]["taxaEntregaDistante"];
							$out["distribuidores"][$pos]["distanciaMaxima"] = $distribuidores[$indexDistribuidor]["distanciaMaxima"];
							$out["distribuidores"][$pos]["valorKmAdicional"] = $distribuidores[$indexDistribuidor]["valorKmAdicional"];

							$feriados = Feriado::where('idDistribuidor', $distribuidores[$indexDistribuidor]['id'])->get()->toArray();

							if(count($feriados)){
								foreach($feriados as $fPos => $f){
									$out["distribuidores"][$pos]["feriados"][$fPos] = $f["dataFeriado"];
								}
							}else{
								$out["distribuidores"][$pos]["feriados"] = [];
							}

							$indexCategoria = 0; $indexProduto = 0; $indexPreco = 0;
							$txtCategoria = $produtos[0]["categoria"];
							$txtProduto = $produtos[0]["idProd"];

							foreach($produtos as $x => $prod){

								if($txtCategoria != $prod["categoria"]){
									$indexCategoria++; $indexProduto = 0; $indexPreco = 0; $txtCategoria = $prod["categoria"];
								}else if($txtProduto != $prod["idProd"]){
									$indexProduto++; $indexPreco = 0; $txtProduto = $prod["idProd"];
								}

								$out["distribuidores"][$pos]["content"][$indexCategoria]["categoria"] = $prod["categoria"];

								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["nome"] = $prod["nome"];
								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["descricao"] = $prod["descricao"];
								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["id"] = $prod["idProd"];
								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["img"] = $prod["img"];
								//if($prod["idCliente"]==null){
									if($indexPreco > 0){
										if($produtos[($x - 1)]["qtdMin"] == $produtos[$x]["qtdMin"]){
											if($produtos[$x]["inicioValidade"] != null || $produtos[$x]["fimValidade"] != null){
												$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["qtd"] = $prod["qtdMin"];
												$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["val"] = $prod["valor"];
											}else if($produtos[$x]["inicioHora"] != null || $produtos[$x]["fimHora"] != null){
												if(!($produtos[($x - 1)]["inicioValidade"] != null || $produtos[($x - 1)]["fimValidade"] != null)){
													$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["qtd"] = $prod["qtdMin"];
													$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["val"] = $prod["valor"];
												}
											}
										}else{
											$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["qtd"] = $prod["qtdMin"];
											$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["val"] = $prod["valor"];
											$indexPreco++;
										}
									}else{
										$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["qtd"] = $prod["qtdMin"];
										$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["val"] = $prod["valor"];
										$indexPreco++;
									}
									$txtProduto = $prod["idProd"];
								//}
							}

						}else{ //CASO O DISTRIBUIDOR NAO POSSUA PRODUTOS ATIVOS
							$out["distribuidores"][$pos]["idDistribuidor"] = null;
						}

					}else{ //SENAO NAO HA DISTRIBUIDOR DISPONIVEL PARA O ENDERECO
						$out["distribuidores"][$pos]["idDistribuidor"] = null;
					}
				}
			}

			header('Content-Type: application/json');
			echo json_encode($out, JSON_PRETTY_PRINT);
		}else{
			$out["msg"] = "not found";
			echo json_encode($out);
		}
	}

    function solicitaContato(){

		$idClientePotencial = $this->escape("id");
		if($idClientePotencial == ""){
			$clientePotencial = new ClientePotencial();
			$date = new DateTime();
			$clientePotencial->dataAcesso	=	$date->format('Y-m-d H:i:s');
			$clientePotencial->latitude		=	$this->escape("latitude");
			$clientePotencial->longitude	=	$this->escape("longitude");
		}else{
			$clientePotencial = ClientePotencial::find($idClientePotencial);
		}

		$clientePotencial->nome		    =	$this->escape("nome");
		$clientePotencial->email		=	$this->escape("email");
		$clientePotencial->telefone 	=	$this->escape("telefone");
		$clientePotencial->endereco		=	$this->escape("endereco");
		$clientePotencial->numero		=	$this->escape("numero");
		$clientePotencial->bairro 		=	$this->escape("bairro");
		$clientePotencial->cidade		=	$this->escape("cidade");
		$clientePotencial->estado		=	$this->escape("estado");
		$clientePotencial->status 		=   ClientePotencial::SOLICITA_CONTATO;

		if($clientePotencial->save()){
			$out["msg"] =  "ok";
		}else{
			$out["msg"] =  "nok";
		}
		echo json_encode($out);
	}

    function enviaEmail(){
        $apiKey = 'key-027dbb9405dc7e439709cf45d01ceda1';
        $domain = "mg.aguasterrasanta.com.br";

        $mgClient = Mailgun::create($apiKey);

		$contato = $this->escape("contato");
		$endereco = $this->escape("endereco");
		$email = $this->escape("email");
		$nome = $this->escape("nome");
		$idUsuario = $this->escape("idUsuario");
		$assunto = $this->escape("assunto");
		$mensagem = $this->escape("mensagem");

		$msg = "O usuário '$nome' ( ID: $idUsuario ) enviou através do seu app TôKumSede a seguinte mensagem:\r\n\r\n$mensagem\r\n\r\n----------------------------------------------\r\nEmail: $email\r\nTelefone: $contato\r\n\r\nEndereço: $endereco";

		$options = array(
		    'from'    => "$nome <mailgun@mg.aguasterrasanta.com.br>",
		    'to'      => 'Contato TôKumSede <contato@tokumsede.com.br>',
		    'subject' => $assunto,
		    'text'    => $msg,
		    'html'	  => ""
		);

		$result = $mgClient->messages()->send($domain, $options);
		$out["msg"] = "ok";
		echo json_encode($out);
	}

    function removerEndereco(){

		$idEndereco = $this->escape("id");
		$out["curPos"] = $this->escape("curPos");
        $endereco = EnderecoCliente::find($idEndereco);

		if($endereco){
			if($endereco->atual){

				$novoAtual = EnderecoCliente::where('idCliente', $endereco->idCliente)
                ->where('atual', 0)
                ->where('status', EnderecoCliente::ATIVO)
                ->first();

				if($novoAtual->count()){

					$nEnd = $novoAtual->fetchOne();
					$nEnd->atual = true;
					$nEnd->save();
					$out["novoAtual"] = $nEnd->id;

				}else{
					$out["novoAtual"] = "nok";
				}

			}else{
				$out["novoAtual"] = "nok";
			}

			$endereco->status = Enderecocliente::EXCLUIDO;
			$endereco->atual = false;
			$endereco->save();

			$out["msg"] = "ok";

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}

    function listImages(){

		$img = Produto::select('img')
        ->where('status', '!=', 3)
        ->whereNotNull('img')
        ->where('img', '!=', '')
        ->groupBy('img')
        ->get()
        ->toArray();

		foreach($img as $pos => $i){
			$out["img"][$pos] = $i["img"];
		}

		echo json_encode($out);

	}

    function consultaInicialSemCadastro(){

		$endereco["cidade"] = $this->escape("cidade");
		$endereco["latitude"] = $this->escape("latitude");
		$endereco["longitude"] = $this->escape("longitude");

        $distrib = Distribuidor::with(['enderecoDistribuidor', 'novoHorarioFuncionamento'])
        ->select('distribuidores.*',
            'endereco_distribuidor.cidade as cidade',
            'endereco_distribuidor.logradouro as logradouro',
            'endereco_distribuidor.estado as estado',
            'endereco_distribuidor.bairro as bairro',
            'endereco_distribuidor.complemento as complemento',
            'endereco_distribuidor.numero as numero',
            'novo_horario_funcionamento.segunda as segunda',
            'novo_horario_funcionamento.inicioSegunda as inicioSegunda',
            'novo_horario_funcionamento.fimSegunda as fimSegunda',
            'novo_horario_funcionamento.terca as terca',
            'novo_horario_funcionamento.inicioTerca as inicioTerca',
            'novo_horario_funcionamento.fimTerca as fimTerca',
            'novo_horario_funcionamento.quarta as quarta',
            'novo_horario_funcionamento.inicioQuarta as inicioQuarta',
            'novo_horario_funcionamento.fimQuarta as fimQuarta',
            'novo_horario_funcionamento.quinta as quinta',
            'novo_horario_funcionamento.inicioQuinta as inicioQuinta',
            'novo_horario_funcionamento.fimQuinta as fimQuinta',
            'novo_horario_funcionamento.sexta as sexta',
            'novo_horario_funcionamento.inicioSexta as inicioSexta',
            'novo_horario_funcionamento.fimSexta as fimSexta',
            'novo_horario_funcionamento.sabado as sabado',
            'novo_horario_funcionamento.inicioSabado as inicioSabado',
            'novo_horario_funcionamento.fimSabado as fimSabado',
            'novo_horario_funcionamento.domingo as domingo',
            'novo_horario_funcionamento.inicioDomingo as inicioDomingo',
            'novo_horario_funcionamento.fimDomingo as fimDomingo',
            'novo_horario_funcionamento.pausaAlmoco as pausaAlmoco',
            'novo_horario_funcionamento.inicioAlmoco as inicioAlmoco',
            'novo_horario_funcionamento.fimAlmoco as fimAlmoco'
        )
        ->where('status', Distribuidor::ATIVO)
        ->get()
        ->toArray();


	foreach($distrib as $pos => $d){
		$out["distrib"][$pos]["nome"] 			= $d["nome"];
		$out["distrib"][$pos]["contato"] 		= "(".$d["dddTelefone"].") ".$d["telefonePrincipal"];
		$out["distrib"][$pos]["email"] 			= $d["email"];
		$out["distrib"][$pos]["logradouro"] 	= $d["logradouro"];
		$out["distrib"][$pos]["numero"] 		= $d["numero"];
		$out["distrib"][$pos]["complemento"]	= $d["complemento"];
		$out["distrib"][$pos]["bairro"] 		= $d["bairro"];
		$out["distrib"][$pos]["cidade"] 		= $d["cidade"];
		$out["distrib"][$pos]["estado"] 		= $d["estado"];

		$out["distrib"][$pos]["horario"][0]["funciona"] = $d["domingo"];
		$out["distrib"][$pos]["horario"][0]["inicio"] 	= $d["inicioDomingo"];
		$out["distrib"][$pos]["horario"][0]["fim"] 		= $d["fimDomingo"];
		$out["distrib"][$pos]["horario"][1]["funciona"] = $d["segunda"];
		$out["distrib"][$pos]["horario"][1]["inicio"] 	= $d["inicioSegunda"];
		$out["distrib"][$pos]["horario"][1]["fim"] 		= $d["fimSegunda"];
		$out["distrib"][$pos]["horario"][2]["funciona"] = $d["terca"];
		$out["distrib"][$pos]["horario"][2]["inicio"] 	= $d["inicioTerca"];
		$out["distrib"][$pos]["horario"][2]["fim"] 		= $d["fimTerca"];
		$out["distrib"][$pos]["horario"][3]["funciona"] = $d["quarta"];
		$out["distrib"][$pos]["horario"][3]["inicio"] 	= $d["inicioQuarta"];
		$out["distrib"][$pos]["horario"][3]["fim"] 		= $d["fimQuarta"];
		$out["distrib"][$pos]["horario"][4]["funciona"] = $d["quinta"];
		$out["distrib"][$pos]["horario"][4]["inicio"] 	= $d["inicioQuinta"];
		$out["distrib"][$pos]["horario"][4]["fim"] 		= $d["fimQuinta"];
		$out["distrib"][$pos]["horario"][5]["funciona"] = $d["sexta"];
		$out["distrib"][$pos]["horario"][5]["inicio"] 	= $d["inicioSexta"];
		$out["distrib"][$pos]["horario"][5]["fim"] 		= $d["fimSexta"];
		$out["distrib"][$pos]["horario"][6]["funciona"] = $d["sabado"];
		$out["distrib"][$pos]["horario"][6]["inicio"] 	= $d["inicioSabado"];
		$out["distrib"][$pos]["horario"][6]["fim"] 		= $d["fimSabado"];
		$out["distrib"][$pos]["pausaAlmoco"] 			= $d["pausaAlmoco"];
		$out["distrib"][$pos]["inicioAlmoco"] 			= $d["inicioAlmoco"];
		$out["distrib"][$pos]["fimAlmoco"]		 		= $d["fimAlmoco"];
	}


		$fator = 0.1; //Raio de aproximadamente 15,66 km

		$distribuidores = Distribuidor::select(
            'distribuidores.*',
            'endereco_distribuidor.cidade as cidade',
            'endereco_distribuidor.latitude as latitude',
            'endereco_distribuidor.longitude as longitude',
            'endereco_distribuidor.cep as cep',
            'endereco_distribuidor.distanciaMaxima as distanciaMaxima',
            'horario_funcionamento.inicioSemana as inicioSemana',
            'horario_funcionamento.fimSemana as fimSemana',
            'horario_funcionamento.inicioSabado as inicioSabado',
            'horario_funcionamento.fimSabado as fimSabado',
            'horario_funcionamento.domingo as domingo',
            'horario_funcionamento.inicioDomingo as inicioDomingo',
            'horario_funcionamento.fimDomingo as fimDomingo',
            'taxa_entrega.taxaUnica as taxaUnica',
            'taxa_entrega.valorTaxaUnica as valorTaxaUnica',
            'taxa_entrega.taxaDomingo as taxaDomingo',
            'taxa_entrega.valorTaxaDomingo as valorTaxaDomingo',
            'taxa_entrega.taxaCompraMinima as taxaCompraMinima',
            'taxa_entrega.valorCompraMinima as valorCompraMinima',
            'taxa_entrega.taxaEntregaDistante as taxaEntregaDistante',
            'taxa_entrega.distanciaMaxima as distanciaMaxima',
            'taxa_entrega.valorKmAdicional as valorKmAdicional'
        )
        ->join('endereco_distribuidor', 'endereco_distribuidor.id_distribuidor', '=', 'distribuidores.id')
        ->join('horario_funcionamento', 'horario_funcionamento.id_distribuidor', '=', 'distribuidores.id')
        ->join('taxa_entrega', 'taxa_entrega.id_distribuidor', '=', 'distribuidores.id')
        ->where('distribuidores.status', Distribuidor::ATIVO)
        ->whereRaw("endereco_distribuidor.latitude + ? >= ?", [$fator, $endereco['latitude']])
        ->whereRaw("endereco_distribuidor.latitude - ? <= ?", [$fator, $endereco['latitude']])
        ->whereRaw("endereco_distribuidor.longitude + ? >= ?", [$fator, $endereco['longitude']])
        ->whereRaw("endereco_distribuidor.longitude - ? <= ?", [$fator, $endereco['longitude']])
        ->get()
        ->toArray();

		if(count($distribuidores) == 0){
			$out["msg"] = "nok";
			$out["distribuidores"][0]["idDistribuidor"] = null;
		}else{
			//DETERMINA DISTRIBUIDOR MAIS PRÓXIMO
			$indexDistribuidor = 0; $dists = array();
			foreach($distribuidores as $pos2 => $d){
				$dist = calcDistancia($d["latitude"], $d["longitude"], $endereco["latitude"], $endereco["longitude"]);
				$dists[$pos2] = array("max" => $d["distanciaMaxima"], "atual" => $dist, "pos" => $pos2);
			}
			$indexDistribuidor = $this->selectDist($dists);

			if($indexDistribuidor != -1){ //CASO O RAIO DE ATENDIMENTO DO DISTRIBUIDOR ALCANCE A LOCALIZACAO DO CLIENTE
				if($distribuidores[$indexDistribuidor]["tipoDistribuidor"]=="revendedor"){
					$produtos = Preco::select(
                        'precos.*',
                        'produtos.id as idProd',
                        'produtos.nome as nome',
                        'produtos.descricao as descricao',
                        'produtos.img as img',
                        'categorias.nome as categoria'
                    )
                    ->leftJoin('produtos', 'precos.id_produto', '=', 'produtos.id')
                    ->leftJoin('categorias', 'produtos.id_categoria', '=', 'categorias.id')
                    ->leftJoin('estoques', 'precos.id_estoque', '=', 'estoques.id')
                    ->where('precos.status', Preco::ATIVO)
                    ->where('precos.id_distribuidor', $distribuidores[$indexDistribuidor]['idDistribuidor'])
                    ->where('estoques.quantidade', '>=', 1)
                    ->where(function($query) {
                        $query->whereNull('precos.inicio_validade')
                            ->orWhere('precos.inicio_validade', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('precos.fim_validade')
                            ->orWhere('precos.fim_validade', '>', now());
                    })
                    ->whereNull('precos.id_cliente')
                    ->orderBy('categorias.nome', 'asc')
                    ->orderBy('produtos.id')
                    ->orderBy('precos.qtd_min', 'asc')
                    ->get()
                    ->toArray();

				}else{
					$produtos = Preco::select(
                        'precos.*',
                        'produtos.id as idProd',
                        'produtos.nome as nome',
                        'produtos.descricao as descricao',
                        'produtos.img as img',
                        'categorias.nome as categoria'
                    )
                    ->leftJoin('produtos', 'precos.id_produto', '=', 'produtos.id')
                    ->leftJoin('categorias', 'produtos.id_categoria', '=', 'categorias.id')
                    ->leftJoin('estoques', 'precos.id_estoque', '=', 'estoques.id')
                    ->where('precos.status', Preco::ATIVO)
                    ->where('precos.id_distribuidor', $distribuidores[$indexDistribuidor]['id'])
                    ->where('estoques.quantidade', '>=', 1)
                    ->where(function($query) {
                        $query->whereNull('precos.inicio_validade')
                            ->orWhere('precos.inicio_validade', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('precos.fim_validade')
                            ->orWhere('precos.fim_validade', '>', now());
                    })
                    ->whereNull('precos.id_cliente')
                    ->orderBy('categorias.nome', 'asc')
                    ->orderBy('produtos.id')
                    ->orderBy('precos.qtd_min', 'asc')
                    ->get()
                    ->toArray();

				}

				if(count($produtos)){
					//MONTA JSON DE PRODUTOS
					$out["distribuidores"][0]["idDistribuidor"] = $distribuidores[$indexDistribuidor]["id"];
					$indexCategoria = 0; $indexProduto = 0; $indexPreco = 0;
					$txtCategoria = $produtos[0]["categoria"];
					$txtProduto = $produtos[0]["idProd"];

					foreach($produtos as $prod){

						if($txtCategoria != $prod["categoria"]){
							$indexCategoria++; $indexProduto = 0; $indexPreco = 0; $txtCategoria = $prod["categoria"];
						}else if($txtProduto != $prod["idProd"]){
							$indexProduto++; $indexPreco = 0; $txtProduto = $prod["idProd"];
						}
						$out["distribuidores"][0]["content"][$indexCategoria]["categoria"] = $prod["categoria"];

						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["nome"] = $prod["nome"];
						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["descricao"] = $prod["descricao"];
						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["id"] = $prod["idProd"];
						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["img"] = $prod["img"];
						//if($prod["idCliente"]==null){
							$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["qtd"] = $prod["qtdMin"];
							$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["val"] = $prod["valor"];
							$indexPreco++;
						//}
						$txtProduto = $prod["idProd"];

					}
					$out["msg"] = "ok";
				}else{ //CASO O DISTRIBUIDOR NAO POSSUA PRODUTOS ATIVOS
					$out["msg"] = "nok";
					$out["distribuidores"][0]["idDistribuidor"] = null;
				}

			}else{ //SENAO NAO HA DISTRIBUIDOR DISPONIVEL PARA O ENDERECO
				$out["msg"] = "nok";
				$out["distribuidores"][0]["idDistribuidor"] = null;
			}
		}

		echo json_encode($out);

	}

	function clientePotencial(){

		$clientePotencial = new Clientepotencial();
		$date = new DateTime();

		$clientePotencial->latitude		=	$this->escape("latitude");
		$clientePotencial->longitude	=	$this->escape("longitude");
		$clientePotencial->dataAcesso	=	$date->format('Y-m-d H:i:s');
		$clientePotencial->endereco		=	$this->escape("endereco");
		$clientePotencial->numero		=	$this->escape("numero");
		$clientePotencial->bairro 		=	$this->escape("bairro");
		$clientePotencial->cidade		=	$this->escape("cidade");
		$clientePotencial->estado		=	$this->escape("estado");
		$clientePotencial->status 		=   ClientePotencial::ACESSO_COMUM;

		if($clientePotencial->save()){
			$out["msg"] =  "ok";
			$out["idCliente"] = $clientePotencial->id;
			echo json_encode($out);
		}else{
			$out["msg"] =  "nok";
			echo json_encode($out);
		}
	}

	function login(Request $request){

		$cliente = Cliente::whereRaw("cliente.email = '$request->email' AND ( cliente.senha = '$request->senha' OR cliente.senha = '".md5($request->senha)."' OR cliente.senha = '".bcrypt($request->senha)."' ) AND cliente.status = ".Cliente::ATIVO)->get();

        if (count($cliente)) {
            $out["msg"] = "ok";
			$out["idCliente"] = $cliente[0]["id"];

			$out["cliente"]["id"] 				= $cliente[0]["id"];
			$out["cliente"]["nome"] 			= $cliente[0]["nome"];
			$out["cliente"]["ddd"] 				= $cliente[0]["dddTelefone"];
			$out["cliente"]["telefone"] 		= $cliente[0]["telefone"];
			$out["cliente"]["sexo"] 			= $cliente[0]["sexo"];
			$out["cliente"]["dataNascimento"] 	= $cliente[0]["dataNascimento"] == null ? "" : $cliente[0]["dataNascimento"];
			$out["cliente"]["email"] 			= $cliente[0]["email"];
			$out["cliente"]["tipoPessoa"] 		= $cliente[0]["tipoPessoa"];
			$out["cliente"]["cpf"] 				= $cliente[0]["cpf"];
			$out["cliente"]["cnpj"] 			= $cliente[0]["cnpj"];
			$out["cliente"]["regId"]			= $cliente[0]["regId"];

        }else{
        	$out["msg"] = "nok";
        }

		echo json_encode($out);

	}

    function refreshRegId(){

		$idCliente = $this->escape("idCliente");
		$regId = $this->escape("regId");

        $cliente = Cliente::find($idCliente);

		if($cliente){

			$cliente->regId = $regId;

			if($cliente->save()){
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok";
			}

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}

    function notificacaoRecebida(){
		$idPedido = $this->escape("idPedido");
        $pedido = Pedido::find($idPedido);
		if($pedido){
			$pedido->statusChange = 0;
			$pedido->save();
		}
	}

    function senhaModoTeste(){

		$senha = $this->escape("senha");

		if($senha == "tokumsedes3cr3t"){
			$out["msg"] = "ok";
		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);
	}

    function alteraEnderecoAtual(){

		$idCliente  = $this->escape("idCliente");
		$idEndereco = $this->escape("idEndereco");

        // Atualiza todos os registros de Enderecocliente com o idCliente para "atual" = 0
        Enderecocliente::where('idCliente', $idCliente)
            ->update(['atual' => 0]);

        // Atualiza o registro específico de Enderecocliente com o id = $idEndereco para "atual" = 1
        Enderecocliente::where('id', $idEndereco)
            ->update(['atual' => 1]);

		$out["msg"]	= "ok";
		echo json_encode($out);

	}

    function cadastrarNovoEndereco(){

		$enderecoCliente = new Enderecocliente();

		$enderecoCliente->logradouro	=	$this->escape("endereco");
		$enderecoCliente->numero		=	$this->escape("numero");
		$enderecoCliente->bairro		=	$this->escape("bairro");
		$enderecoCliente->complemento	=	$this->escape("complemento");
		$enderecoCliente->cep			=	$this->escape("cep");
		$enderecoCliente->cidade 		=	$this->escape("cidade");
		$enderecoCliente->estado		=	$this->escape("estado");
		$enderecoCliente->referencia 	=	$this->escape("referencia");
		$enderecoCliente->apelido		=	$this->escape("apelido");
		$enderecoCliente->atual			=   true;
		$enderecoCliente->idCliente		=   $this->escape("idCliente");
		$enderecoCliente->latitude		=	$this->escape("latitude");
		$enderecoCliente->longitude		=	$this->escape("longitude");
		$enderecoCliente->status		=	Enderecocliente::ATIVO;

		if($enderecoCliente->save()){

			Enderecocliente::where('idCliente', $enderecoCliente->idCliente)
            ->where('id', '!=', $enderecoCliente->id)
            ->update(['atual' => 0]);


			$this->consultaInicial($enderecoCliente->idCliente);

		}else{
			$out["msg"] = "nok";
			echo json_encode($out);
		}

	}

    function cancelarPedido(){

		$idPedido = $this->escape("idPedido");
        $pedido = Pedido::find($idPedido);

		if($pedido){

			$pedido->status = Pedido::CANCELADO_USUARIO;

			if($pedido->save()){
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok";
			}

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}

    function pedidoRecebido(){

		$idPedido = $this->escape("idPedido");
        $pedido = Pedido::find($idPedido);
		$date = new DateTime();
		// //Buscar distribuidor e cliente ***PREMIAÇÕES
		// if($premiacoes){
		// 	$distribuidor = Doctrine::getTable("Distribuidor")->findOneById($pedido->idDistribuidor);
		// 	$enderecoCliente = Doctrine::getTable("Enderecocliente")->findOneById($pedido->idEndereco);
		// 	$cliente = Doctrine::getTable("Cliente")->findOneById($enderecoCliente->idCliente);
		// }
		if($pedido){

			//ALTERA OS ESTOQUES
	        if($pedido->status == Pedido::ACEITO){
				$itensPedido = ItemPedido::where('idPedido', $idPedido)->get()->toArray();


	            for ($i = 0; $i < sizeof($itensPedido); $i++) {
	                $estoques = Estoque::where('idDistribuidor', $pedido->idDistribuidor == 23 ? 14 : $pedido->idDistribuidor)
                    ->where('idProduto', $itensPedido[$i]['idProduto'])
                    ->get()
                    ->toArray();
                    $estoque = Estoque::find($estoques[0]['id']);
	                $estoque->quantidade -= $itensPedido[$i]["qtd"];
					$estoque->save();

					// //PREMIAÇÕES
					// if($itensPedido[$i]["idPedido"]==1 || $itensPedido[$i]["idPedido"]==4 || $itensPedido[$i]["idPedido"]==5 || $itensPedido[$i]["idPedido"]==6){
					// 	$premiacoes?$cliente->pontuacao+=$itensPedido[$i]["qtd"]:'';
					// }
					// //**************************** */
	            }
			}

			$pedido->status = Pedido::ENTREGUE;
			$pedido->horarioEntrega = $date->format('Y-m-d H:i:s');

			if($pedido->save()){
				// //PREMIAÇÕES
				// if($premiacoes && $cliente->tipoPessoa==1){
				// 	$cliente->pontuacaoAcumulada+=$pedido->pontuacao;
				// 	$cliente->pontuacao+=$pedido->pontuacao;
				// 	if($cliente->pontuacao>=10){
				// 		$distribuidor->premiacoesEntregues+=intval($cliente->pontuacao/10);
				// 		$cliente->premios+=intval($cliente->pontuacao/10);
				// 		$cliente->pontuacao%=10;
				// 		$distribuidor->save();
				// 	}
				// 	$cliente->save();
				// }
				// //*****
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok";
			}

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}

    function alteraDadosCliente(){

		$idCliente = $this->escape("id");
        $cliente = Cliente::find($idCliente);

		if($cliente){

			$senha = $this->escape("senha");

			$cliente->nome 			=	$this->escape("nome");
			$cliente->dddTelefone	=	$this->escape("ddd");
			$cliente->telefone		=	$this->escape("telefone");
			$cliente->sexo			=	$this->escape("sexo");
			$cliente->dataNascimento=	$this->escape("dataNascimento");
			$cliente->senha			=	$senha == "" ? $cliente->senha : $senha;
			$cliente->tipoPessoa 	=	$this->escape("tipoPessoa");
			$cliente->cpf			=	$this->escape("cpf");
			$cliente->cnpj			=	$this->escape("cnpj");

			if($cliente->save()){
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok";
			}

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}

    function verifyRecover(){

		$email 		= $this->escape("email");
		$ddd 		= $this->escape("ddd");
		$telefone 	= $this->escape("telefone");

		$cliente = Cliente::select('clientes.*')
        ->where('clientes.email', $email)
        ->where('clientes.dddTelefone', $ddd)
        ->where('clientes.telefone', $telefone)
        ->where('clientes.status', Cliente::ATIVO)
        ->get()
        ->toArray();


        if (count($cliente)) {
        	$out["msg"] = "ok";
			$out["idCliente"] = $cliente[0]["id"];

			$out["cliente"]["id"] 				= $cliente[0]["id"];
			$out["cliente"]["nome"] 			= $cliente[0]["nome"];
			$out["cliente"]["ddd"] 				= $cliente[0]["dddTelefone"];
			$out["cliente"]["telefone"] 		= $cliente[0]["telefone"];
			$out["cliente"]["sexo"] 			= $cliente[0]["sexo"];
			$out["cliente"]["dataNascimento"] 	= $cliente[0]["dataNascimento"] == null ? "" : $cliente[0]["dataNascimento"];
			$out["cliente"]["email"] 			= $cliente[0]["email"];
			$out["cliente"]["tipoPessoa"] 		= $cliente[0]["tipoPessoa"];
			$out["cliente"]["cpf"] 				= $cliente[0]["cpf"];
			$out["cliente"]["cnpj"] 			= $cliente[0]["cnpj"];
			$out["cliente"]["regId"]			= $cliente[0]["regId"];
        }else{
        	$out["msg"] = "nok";
        }

		echo json_encode($out);

	}

    function alteraSenha(){

		$idCliente = $this->escape("idCliente");
		$senha = $this->escape("senha");

        $cliente = Cliente::find($idCliente);

		if($cliente){

			$cliente->senha = $senha;

			if($cliente->save()){

				$out["msg"] = "ok";
				echo json_encode($out);

			}else{
				$out["msg"] = "nok";
				echo json_encode($out);
			}

		}else{
			$out["msg"] = "nok";
			echo json_encode($out);
		}

	}

    function novoPedido(){

		$pedido = new Pedido();
        $timezone = new DateTimeZone('America/Recife');

		$date = new DateTime('now', $timezone);

		$pedido->idDistribuidor = $this->escape("idDistribuidor");
		$pedido->total 			= $this->escape("totalPedido");
		$pedido->taxaEntrega	= $this->escape("taxaEntrega");
		$pedido->formaPagamento = $this->escape("formaPagamento");
		$pedido->trocoPara 		= $this->escape("trocoPara");
		$pedido->horarioPedido 	= $date->format('Y-m-d H:i:s');
		$pedido->status 		= Pedido::PENDENTE;
		$pedido->statusChange	= 0;
		$pedido->idEndereco 	= $this->escape("idEndereco");
		$pedido->agendado 		= (int)$this->escape("agendado");
		if($pedido->agendado){
			$pedido->dataAgendada 	= $this->escape("dataAgendada");
			$pedido->horaInicio 	= $this->escape("horaInicio");
			$pedido->horaFim 		= $this->escape("horaFim");
		}
		$pedido->origem 		= $this->escape("origem");
		$pedido->obs 			= $this->escape("obs");

		$produtos = rawurldecode($this->escape("produtos"));
		$produtos = json_decode($produtos);
        // Buscar o distribuidor pelo id
        $distribuidor = Distribuidor::find($pedido->idDistribuidor);

        // Buscar o endereço do cliente pelo id
        $endereco = EnderecoCliente::find($pedido->idEndereco);

        // Buscar o cliente associado ao endereço
        $cliente = Cliente::find($endereco->idCliente);
		if($distribuidor->tipoDistribuidor=="revendedor"){
			//$prodRevendedor = array(1, 4, 5, 6); //ID dos produtos do revendedor
			foreach ($produtos as $produto) {
				if ($produto->produto->id != 1 && $produto->produto->id != 4 && $produto->produto->id != 5 && $produto->produto->id != 6) {//prodRevendedor.indexOf(parseInt($produto->produto->id)) == -1
					$pedido->idDistribuidor = $distribuidor->idDistribuidor;
				}
			}
		}
		// if($pedido->idDistribuidor==23){
		// 	$pedido->idDistribuidor=14;
		// }
		if($pedido->save()){
			// //PREMIAÇÕES
			// $aguaPl=false;
			// $valorAgua=null;
			// //*********** */
			foreach($produtos as $p){

				$item = new Itempedido();

				$item->qtd = $p->qtd;
				$item->preco = $p->preco;
				$item->subtotal = $p->subtotal;
				$item->idProduto = $p->produto->id;
				$item->idPedido = $pedido->id;

				$item->save();
				// //PREMIAÇÕES PONTUAÇÃO DO PEDIDO
				// if($premiacoes && $cliente->tipoPessoa==1){
				// 	if($item->idProduto==1){//PL
				// 		$pedido->pontuacao+=$item->qtd;
				// 		$valorAgua=$item->preco;
				// 		$aguaPl=true;
				// 	}else if($item->idProduto==4){//RICA
				// 		$pedido->pontuacao+=$item->qtd;
				// 		!$valorAgua?$valorAgua=$item->preco:'';
				// 	}else if($item->idProduto==5){//PL+GARRAFAO
				// 		$pedido->pontuacao+=$item->qtd;
				// 		$aguaPl=true;
				// 	}else if($item->idProduto==6){//RICA+GARRAFAO
				// 		$pedido->pontuacao+=$item->qtd;
				// 	}
				// }
				// //**************************** */
			}
			// //PREMIAÇÕES DESCONTO NO PEDIDO
			// if($premiacoes && $cliente->tipoPessoa==1){
			// 	$pedido->pontuacaoAcumulada=$cliente->pontuacao+$pedido->pontuacao;
			// 	if($pedido->pontuacaoAcumulada>=10){
			// 		$premios=intval($pedido->pontuacaoAcumulada/10);
			// 		if($valorAgua){
			// 			$pedido->descontoPremiacao=$premios*$valorAgua;
			// 		}else{
			// 			//buscar valor
			// 			$aguaPl?$preco=Doctrine_Query::create()->select("p.valor")->from("Preco p")->where("status=1 AND idProduto=1 AND valor>0 AND idDistribuidor=".$pedido->idDistribuidor)->execute()->toArray():$preco=Doctrine_Query::create()->select("p.*")->from("Preco p")->where("status=1 AND idProduto=4 AND valor>0 AND idDistribuidor=".$pedido->idDistribuidor)->execute()->toArray();
			// 			$pedido->descontoPremiacao=$preco[0]['valor'];
			// 		}
			// 		$pedido->total-=$pedido->descontoPremiacao;
			// 	}
			// 	$pedido->save();
			// }
			// //****************************** */

			$out["msg"] = "ok";
			$out["idPedido"] = $pedido->id;
			$administradores = Administrador::where('status', 'Ativo')
            ->where(function($query) use ($pedido) {
                $query->where('tipoAdministrador', 'Administrador')
                      ->orWhere('tipoAdministrador', 'Atendente')
                      ->orWhere('idDistribuidor', $pedido->idDistribuidor);
            })
            ->get()
            ->toArray();

            // API access key from Google API's Console
            $texto = $endereco->logradouro.' '.$endereco->numero.', '.$endereco->bairro.' - '.$endereco->cidade.'/'.$endereco->estado;
            // prep the bundle
            $msg = array(
                'title' => 'Pedido '.$pedido->id.' - '.$cliente->nome,
                'body' => $texto,
                'tag' => $pedido->id,
                'icon' => '/images/logo-icon.png',
                'click_action' => 'https://tks.tokumsede.com.br'
            );
            $headers = array(
                'Authorization: key=AAAA92nZhZY:APA91bFbwC0HrbjmBGjQIrXtPrPZcH5gmCFK9y1jlQucH03VlNOHlO45Ru5Dk69iplWGYcnsVUbhG2hMH5AgoZzU9GCK0DmFplBjLz-QAmlFM5YOpmFFOr5ak--7l-yLahiaJKPPIUct',
                'Content-Type: application/json',
								"Access-Control-Allow-Origin: *"
			);
            foreach($administradores as $p => $administrador) {
                // set only for one for safety
                if($administrador['token_fcm']!=null){
					$fields = array(
						'priority' => 'high',
						'to' => $administrador['token_fcm'],
						'notification' => $msg
					);
					$ch = curl_init();
					curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
					curl_setopt( $ch,CURLOPT_POST, true );
					curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
					curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
					curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true );
					curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
					$result = curl_exec($ch );
					curl_close( $ch );
                }
                // set only for one for safety
                if($administrador['token_fcm_mobile']!=null){
                    $fields = array(
						'priority' => 'high',
						'to' => $administrador['token_fcm_mobile'],
						'notification' => $msg
					);
					$ch = curl_init();
					curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
					curl_setopt( $ch,CURLOPT_POST, true );
					curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
					curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
					curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true );
					curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
					$result = curl_exec($ch );
					curl_close( $ch );
                }
			}

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}
    // ---------------------------------- Refatorados ------------------------------------------------//

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

	function gcmStatusPedido(){

		$idCliente = $this->escape("idCliente");
		$idPedido = $this->escape("idPedido");

        $cliente = Cliente::find($idCliente);
        $pedido = Pedido::find($idPedido);

		if($this->gcmSend($cliente->regId, $idCliente, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true)){
			echo "ok".PHP_EOL;
		}else{
			echo "nok".PHP_EOL;
		}

	}

	function cadastraCliente(){

		$cliente = new Cliente();

		$dataNascimento = $this->escape("dataNascimento");

		$cliente->nome 			=	$this->escape("nome");
		$cliente->dddTelefone	=	$this->escape("ddd");
		$cliente->telefone		=	$this->escape("telefone");
		$cliente->sexo			=	$this->escape("sexo");
		$cliente->dataNascimento=	$dataNascimento == "" ? null : $dataNascimento;
		$cliente->email			=	strtolower($this->escape("email"));
		$cliente->senha			=	$this->escape("senha");
		$cliente->tipoPessoa 	=	$this->escape("tipoPessoa");
		$cliente->cpf			=	$this->escape("cpf");
		$cliente->cnpj			=	$this->escape("cnpj");
		$cliente->logado		=   false;
		$cliente->rating		= 	0;
		$cliente->status		=	Cliente::ATIVO;

		if($cliente->save()){

			$enderecoCliente = new Enderecocliente();

			$enderecoCliente->logradouro	=	$this->escape("endereco");
			$enderecoCliente->numero		=	$this->escape("numero");
			$enderecoCliente->bairro		=	$this->escape("bairro");
			$enderecoCliente->complemento	=	$this->escape("complemento");
			$enderecoCliente->cep			=	$this->escape("cep");
			$enderecoCliente->cidade 		=	$this->escape("cidade");
			$enderecoCliente->estado		=	$this->escape("estado");
			$enderecoCliente->referencia 	=	$this->escape("referencia");
			$enderecoCliente->apelido		=	$this->escape("apelido");
			$enderecoCliente->atual			=   true;
			$enderecoCliente->idCliente		=   $cliente->id;
			$enderecoCliente->latitude		=	$this->escape("latitude");
			$enderecoCliente->longitude		=	$this->escape("longitude");
			$enderecoCliente->status		=	Enderecocliente::ATIVO;

			if($enderecoCliente->save()){
				$out["msg"] 		=  "ok";
				$out["idCliente"] 	= $cliente->id;

				$out["cliente"]["id"] 				= $cliente->id;
				$out["cliente"]["nome"] 			= $cliente->nome;
				$out["cliente"]["ddd"] 				= $cliente->dddTelefone;
				$out["cliente"]["telefone"] 		= $cliente->telefone;
				$out["cliente"]["sexo"] 			= $cliente->sexo;
				$out["cliente"]["dataNascimento"] 	= $cliente->dataNascimento == null ? "" : $cliente->dataNascimento;
				$out["cliente"]["email"] 			= $cliente->email;
				$out["cliente"]["tipoPessoa"] 		= $cliente->tipoPessoa;
				$out["cliente"]["cpf"] 				= $cliente->cpf;
				$out["cliente"]["cnpj"] 			= $cliente->cnpj;
				$out["cliente"]["regId"]			= $cliente->regId;

				echo json_encode($out);
			}else{
				$out["msg"] =  "nok2";
				echo json_encode($out);
			}

		}else{
			$out["msg"] =  "nok1";
			echo json_encode($out);
		}

	}

	function listUnusedImages(){
		foreach (glob("/var/www/tokumsede/view/imgs/uploads/*") as $filename) {

			$split = explode('/', $filename);
			$file = $split[count($split)-1];

			$img = Doctrine_Query::create()
                ->select("p.img as img, p.nome as nome, p.status as status")
                ->from("Produto p")
				->where("p.img = '$file' AND p.status != 3")
                ->execute()
                ->toArray();

			if(!count($img)){
				echo $file."<br />";
			}
		}
	}

	function duplicaProdutos(){
		$precos = Doctrine_Query::create()
	            ->select("pre.*")
	            ->from("Preco pre")
				->where("pre.status = ".Preco::ATIVO." AND pre.idDistribuidor = 9")
	            ->execute()
	            ->toArray();

		$ok = 0; $erro = 0;
		foreach($precos as $preco){

			$estoque = Doctrine_Query::create()
                ->from('Estoque')
                ->where('idDistribuidor = 8 AND idProduto = ' . $preco["idProduto"]);

			if($estoque->count() && false){
				$e = $estoque->fetchOne();

				$p = new Preco();
				$p->idProduto = $preco["idProduto"];
				$p->idDistribuidor = 8;
				$p->idEstoque = $e->id;
				$p->valor = $preco["valor"];
				$p->qtdMin = $preco["qtdMin"];
				$p->inicioValidade = $preco["inicioValidade"];
				$p->fimValidade = $preco["fimValidade"];
				$p->status = $preco["status"];

				if ($p->save()) {
					$ok++;
				}else{
					$erro++;
				}

			}else{
				$erro++;
			}

		}

		echo "Produtos OK: $ok  -  Produtos NOK: $erro";

	}

	function consultaCoords(){
		$enderecoCliente = Doctrine::getTable("Enderecocliente")->findOneById(12);
		$coordenadas = buscarLatitudeLongitude($enderecoCliente->logradouro, $enderecoCliente->numero, $enderecoCliente->cidade, $enderecoCliente->estado, $enderecoCliente->cep);

		echo json_encode($coordenadas);
	}
}

function calcDistancia($lat1, $long1, $lat2, $long2){

    $d2r = 0.017453292519943295769236;

    $dlong = ($long2 - $long1) * $d2r;
    $dlat = ($lat2 - $lat1) * $d2r;

    $temp_sin = sin($dlat/2.0);
    $temp_cos = cos($lat1 * $d2r);
    $temp_sin2 = sin($dlong/2.0);

    $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
    $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

    return 6368.1 * $c;
}

function buscarLatitudeLongitude($logradouro, $numero, $cidade, $estado, $cep) {
    //$address = {nm_bairro}.", ".{nm_cidade}.", ".{nm_estado}.", ".{nm_brasil};
    $key = "AIzaSyDIt2CSa_K8P64daT3v4Hv8Ml-8IJsFic8";
    $address = $logradouro . ", " . $numero . ", " . $cidade . ", " . $estado . ", " . $cep . "," . "Brasil";
    $request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address=" . $address . "&sensor=true&key=".$key; // A URL que vc manda pro google para pegar o XML
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
