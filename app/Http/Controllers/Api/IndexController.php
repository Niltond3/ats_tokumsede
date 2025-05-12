<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Mail;
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

    /**
     * Get the effective distributor ID.
     * This might be the ID of a main distributor if the current one is part of a union.
     * This method should be identical to the one in produtoController.php or moved to a Trait/BaseController.
     *
     * @param int $distributorId
     * @return int
     */
  private function getEffectiveDistributorId($distributorId)
    {
        $distributor = Distribuidor::find($distributorId);
        return $distributor->getMainDistributorIdAttribute() ?? $distributorId;
    }

    private function getActiveProducts($distribuidorId)
{
    // Get effective distributor ID (main distributor if in union)
    $effectiveDistributorId = $this->getEffectiveDistributorId($distribuidorId);

    return Preco::selectRaw("preco.*, produto.id as idProd, produto.nome as nome, produto.descricao as descricao, produto.img as img, categoria.nome as categoria, estoque.id as idEstoque")
    ->leftJoin("produto", "produto.id", "=", "preco.idProduto")
    ->leftJoin("categoria", "categoria.id", "=", "produto.idCategoria")
    ->leftJoin('estoque', function($join) use ($effectiveDistributorId) {
            $join->on('estoque.idProduto', '=', 'produto.id')
                 ->where('estoque.idDistribuidor', '=', $effectiveDistributorId);
        })
    ->where([
            ['produto.status', '=', Produto::ATIVO],
            ['preco.idDistribuidor', '=', $distribuidorId],
            ['preco.status', '=', 1],
            ['estoque.quantidade', '>', 0],
        ])
        ->where(function ($query) {
            $query->whereNull('preco.inicioValidade')
                ->orWhere('preco.inicioValidade', '<=', DB::raw('curdate()'));
        })
        ->where(function ($query) {
            $query->whereNull('preco.fimValidade')
                ->orWhere('preco.fimValidade', '>', DB::raw('curdate()'));
        })
    ->orderByRaw("categoria.nome ASC, produto.nome, preco.qtdMin ASC")
    ->get();
}
private function getAllActiveProducts($distribuidorId)
{
    $effectiveDistributorId = $this->getEffectiveDistributorId($distribuidorId);

    $products = DB::table('produto')
        ->leftJoin('preco', function($join) use ($distribuidorId) {
            $join->on('produto.id', '=', 'preco.idProduto')
                 ->where('preco.idDistribuidor', '=', $distribuidorId);
        })
        ->leftJoin('estoque', function($join) use ($effectiveDistributorId) {
            $join->on('estoque.idProduto', '=', 'produto.id')
                 ->where('estoque.idDistribuidor', '=', $effectiveDistributorId);
        })
        ->select([
            DB::raw('COALESCE(preco.id, 0) as id'),
            DB::raw('COALESCE(preco.idProduto, produto.id) as idProduto'),
            DB::raw('COALESCE(preco.idDistribuidor, ' . $distribuidorId . ') as idDistribuidor'),
            DB::raw('COALESCE(preco.idCliente, NULL) as idCliente'),
            DB::raw('COALESCE(preco.valor, 0) as valor'),
            DB::raw('COALESCE(preco.qtdMin, 0) as qtdMin'),
            'preco.inicioValidade',
            'preco.fimValidade',
            'preco.status',
            DB::raw('COALESCE(preco.id, 0) as idPreco'),
            'produto.id as idProd',
            'produto.nome as nome',
            'produto.img as img',
            'produto.descricao as descricao'
        ])
        ->where('produto.status', '=', Produto::ATIVO)
        ->where(function ($query) {
            $query->whereNull('preco.inicioValidade')
                ->orWhere('preco.inicioValidade', '<=', DB::raw('curdate()'));
        })
        ->where(function ($query) {
            $query->whereNull('preco.fimValidade')
                ->orWhere('preco.fimValidade', '>', DB::raw('curdate()'));
        })
        ->orderByRaw("CASE
            WHEN produto.nome LIKE '%Alkalina%' THEN 1
            WHEN produto.nome LIKE '%20L%' THEN 2
            ELSE 3
        END")
        ->orderBy('produto.nome')
        ->orderBy('produto.id')
        ->orderBy('preco.valor')
        ->orderBy('preco.qtdMin')
        ->get();

    return $products;
}
    function verificaPedidoAlterado(Request $request){

		$idCliente = $request->idCliente;
		$cliente = Cliente::find($idCliente);

		if($cliente){
			$pedidos = Pedido::select("pedido.*")
						->leftJoin('enderecoCliente', 'enderecoCliente.id', '=', 'pedido.idEndereco')
						->leftJoin('cliente', 'cliente.id', '=', 'enderecoCliente.idCliente')
						->where([["statusChange","1"],['cliente.id',$idCliente]])
						->get();
			if(count($pedidos)){
				foreach($pedidos as $p){
					$this->gcmSend($cliente->regId, $idCliente, $p["id"], $p["status"], $p["retorno"], $p["origem"], false);
				}
			}
		}

	}

    function verificaEmail(Request $request) {

        $users = Cliente::where([['email',$request->email],['status',Cliente::ATIVO]])->get();

		$out["qtd"] = count($users);
		echo json_encode($out);

    }

    function consultaInicial(Request $request){//$clie == null
		$idCliente  = $request->idCliente;
		$system 	= $request->system;
		$cliente 	= Cliente::find($idCliente);

		$appVersion	= $request->appVersion?$request->appVersion:$cliente->appVersion;

		$distrib = Distribuidor::where('status',Distribuidor::ATIVO)
			->with('novoHorarioFuncionamento','enderecoDistribuidor:id,cidade,logradouro,estado,bairro,complemento,numero')
			->get();

		foreach($distrib as $pos => $d){
			$out["distrib"][$pos]["nome"] 			= $d["nome"];
			$out["distrib"][$pos]["contato"] 		= "(".$d["dddTelefone"].") ".$d["telefonePrincipal"];
			$out["distrib"][$pos]["email"] 			= $d["email"];
			$out["distrib"][$pos]["logradouro"] 	= $d['enderecoDistribuidor']["logradouro"];
			$out["distrib"][$pos]["numero"] 		= $d['enderecoDistribuidor']["numero"];
			$out["distrib"][$pos]["complemento"]	= $d['enderecoDistribuidor']["complemento"];
			$out["distrib"][$pos]["bairro"] 		= $d['enderecoDistribuidor']["bairro"];
			$out["distrib"][$pos]["cidade"] 		= $d['enderecoDistribuidor']["cidade"];
			$out["distrib"][$pos]["estado"] 		= $d['enderecoDistribuidor']["estado"];

			$out["distrib"][$pos]["horario"][0]["funciona"] = (string)$d['novoHorarioFuncionamento']["domingo"];
			$out["distrib"][$pos]["horario"][0]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioDomingo"];
			$out["distrib"][$pos]["horario"][0]["fim"] 		= $d['novoHorarioFuncionamento']["fimDomingo"];
			$out["distrib"][$pos]["horario"][1]["funciona"] = (string)$d['novoHorarioFuncionamento']["segunda"];
			$out["distrib"][$pos]["horario"][1]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioSegunda"];
			$out["distrib"][$pos]["horario"][1]["fim"] 		= $d['novoHorarioFuncionamento']["fimSegunda"];
			$out["distrib"][$pos]["horario"][2]["funciona"] = (string)$d['novoHorarioFuncionamento']["terca"];
			$out["distrib"][$pos]["horario"][2]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioTerca"];
			$out["distrib"][$pos]["horario"][2]["fim"] 		= $d['novoHorarioFuncionamento']["fimTerca"];
			$out["distrib"][$pos]["horario"][3]["funciona"] = (string)$d['novoHorarioFuncionamento']["quarta"];
			$out["distrib"][$pos]["horario"][3]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioQuarta"];
			$out["distrib"][$pos]["horario"][3]["fim"] 		= $d['novoHorarioFuncionamento']["fimQuarta"];
			$out["distrib"][$pos]["horario"][4]["funciona"] = (string)$d['novoHorarioFuncionamento']["quinta"];
			$out["distrib"][$pos]["horario"][4]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioQuinta"];
			$out["distrib"][$pos]["horario"][4]["fim"] 		= $d['novoHorarioFuncionamento']["fimQuinta"];
			$out["distrib"][$pos]["horario"][5]["funciona"] = (string)$d['novoHorarioFuncionamento']["sexta"];
			$out["distrib"][$pos]["horario"][5]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioSexta"];
			$out["distrib"][$pos]["horario"][5]["fim"] 		= $d['novoHorarioFuncionamento']["fimSexta"];
			$out["distrib"][$pos]["horario"][6]["funciona"] = (string)$d['novoHorarioFuncionamento']["sabado"];
			$out["distrib"][$pos]["horario"][6]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioSabado"];
			$out["distrib"][$pos]["horario"][6]["fim"] 		= $d['novoHorarioFuncionamento']["fimSabado"];
			$out["distrib"][$pos]["pausaAlmoco"] 			= (string)$d['novoHorarioFuncionamento']["pausaAlmoco"];
			$out["distrib"][$pos]["inicioAlmoco"] 			= $d['novoHorarioFuncionamento']["inicioAlmoco"];
			$out["distrib"][$pos]["fimAlmoco"]		 		= $d['novoHorarioFuncionamento']["fimAlmoco"];
		}

		if($cliente && $cliente->status == Cliente::ATIVO){

			if($system != null){
				$cliente->appVersion = $system." => ".$appVersion;
			}
			$cliente->ultimoLogin = now();
			$cliente->save();

			$out["msg"] = "ok";
			$enderecos = EnderecoCliente::where([['idCliente',$idCliente],['status',Enderecocliente::ATIVO]])->get();

			$out["freeTax"] = array(1, 4, 5, 6, 332, 333, 335); //ID dos produtos que geram liberação de Taxa de Entrega
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

				$joinHorario = $appVersion==null?"horarioFuncionamento":'novoHorarioFuncionamento';

				$distribuidores = Distribuidor::selectRaw("distribuidor.*, enderecoDistribuidor.cidade as cidade, enderecoDistribuidor.latitude as latitude, enderecoDistribuidor.longitude as longitude, enderecoDistribuidor.cep as cep, enderecoDistribuidor.distanciaMaxima as distanciaMaxima")
					->Join("enderecoDistribuidor", 'enderecoDistribuidor.id', '=','distribuidor.idEnderecoDistribuidor')
					->Join("taxaEntrega", 'taxaEntrega.id', '=','distribuidor.idTaxaEntrega')
					->Join($joinHorario, $joinHorario.'.id', '=','distribuidor.id'.$joinHorario)
					->whereRaw("distribuidor.status = ".Distribuidor::ATIVO." AND enderecoDistribuidor.latitude + $fator >= ".$endereco["latitude"]." AND enderecoDistribuidor.latitude - $fator <= ".$endereco["latitude"]." AND enderecoDistribuidor.longitude + $fator >= ".$endereco["longitude"]." AND enderecoDistribuidor.longitude - $fator <= ".$endereco["longitude"])
					->get();

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

						$idDistribuidor = $distribuidores[$indexDistribuidor]["tipoDistribuidor"]=="revendedor"?$distribuidores[$indexDistribuidor]["idDistribuidor"]:$distribuidores[$indexDistribuidor]["id"];

                        $effectiveDistributorId = $this->getEffectiveDistributorId($idDistribuidor);

						$produtos = $this->getActiveProducts($idDistribuidor);

    // ->leftJoin("produto", "produto.id", "=", "preco.idProduto")
    // ->leftJoin("categoria", "categoria.id", "=", "produto.idCategoria")
    // ->leftJoin('estoque', function($join) use ($effectiveDistributorId) {
    //         $join->on('estoque.idProduto', '=', 'produto.id')
    //              ->where('estoque.idDistribuidor', '=', $effectiveDistributorId);
    //     })
    // ->where([
    //         ['produto.status', '=', Produto::ATIVO],
    //         ['preco.idDistribuidor', '=', $effectiveDistributorId],
    //         ['preco.status', '=', 1],
    //         ['estoque.quantidade', '>', 0],
    //     ])
    //     ->where(function ($query) {
    //         $query->whereNull('preco.inicioValidade')
    //             ->orWhere('preco.inicioValidade', '<=', DB::raw('curdate()'));
    //     })
    //     ->where(function ($query) {
    //         $query->whereNull('preco.fimValidade')
    //             ->orWhere('preco.fimValidade', '>', DB::raw('curdate()'));
    //     })
    // ->orderByRaw("categoria.nome ASC, produto.nome, preco.qtdMin ASC")
    // ->get();
    //

						if(count($produtos)){
							//MONTA JSON DE PRODUTOS
							$out["distribuidores"][$pos]["idDistribuidor"] = $distribuidores[$indexDistribuidor]["id"];
							$out["distribuidores"][$pos]["contato"] = "(".$distribuidores[$indexDistribuidor]["dddTelefone"].") ".$distribuidores[$indexDistribuidor]["telefonePrincipal"];
							if($appVersion == null){
								$out["distribuidores"][$pos]["inicioSemana"] 	= $distribuidores[$indexDistribuidor]['horarioFuncionamento']["inicioSemana"];
								$out["distribuidores"][$pos]["fimSemana"] 		= $distribuidores[$indexDistribuidor]['horarioFuncionamento']["fimSemana"];
								$out["distribuidores"][$pos]["inicioSabado"] 	= $distribuidores[$indexDistribuidor]['horarioFuncionamento']["inicioSabado"];
								$out["distribuidores"][$pos]["fimSabado"] 		= $distribuidores[$indexDistribuidor]['horarioFuncionamento']["fimSabado"];
								$out["distribuidores"][$pos]["domingo"] 		= (bool)$distribuidores[$indexDistribuidor]['horarioFuncionamento']["domingo"];
								$out["distribuidores"][$pos]["inicioDomingo"] 	= $distribuidores[$indexDistribuidor]['horarioFuncionamento']["inicioDomingo"];
								$out["distribuidores"][$pos]["fimDomingo"] 		= $distribuidores[$indexDistribuidor]['horarioFuncionamento']["fimDomingo"];
							}else{
								$out["distribuidores"][$pos]["horario"][0]["funciona"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["domingo"];
								$out["distribuidores"][$pos]["horario"][0]["inicio"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioDomingo"];
								$out["distribuidores"][$pos]["horario"][0]["fim"] 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimDomingo"];
								$out["distribuidores"][$pos]["horario"][1]["funciona"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["segunda"];
								$out["distribuidores"][$pos]["horario"][1]["inicio"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioSegunda"];
								$out["distribuidores"][$pos]["horario"][1]["fim"] 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimSegunda"];
								$out["distribuidores"][$pos]["horario"][2]["funciona"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["terca"];
								$out["distribuidores"][$pos]["horario"][2]["inicio"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioTerca"];
								$out["distribuidores"][$pos]["horario"][2]["fim"] 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimTerca"];
								$out["distribuidores"][$pos]["horario"][3]["funciona"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["quarta"];
								$out["distribuidores"][$pos]["horario"][3]["inicio"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioQuarta"];
								$out["distribuidores"][$pos]["horario"][3]["fim"] 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimQuarta"];
								$out["distribuidores"][$pos]["horario"][4]["funciona"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["quinta"];
								$out["distribuidores"][$pos]["horario"][4]["inicio"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioQuinta"];
								$out["distribuidores"][$pos]["horario"][4]["fim"] 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimQuinta"];
								$out["distribuidores"][$pos]["horario"][5]["funciona"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["sexta"];
								$out["distribuidores"][$pos]["horario"][5]["inicio"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioSexta"];
								$out["distribuidores"][$pos]["horario"][5]["fim"] 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimSexta"];
								$out["distribuidores"][$pos]["horario"][6]["funciona"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["sabado"];
								$out["distribuidores"][$pos]["horario"][6]["inicio"] 	= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioSabado"];
								$out["distribuidores"][$pos]["horario"][6]["fim"] 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimSabado"];
								$out["distribuidores"][$pos]["pausaAlmoco"] 			= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["pausaAlmoco"];
								$out["distribuidores"][$pos]["inicioAlmoco"] 			= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["inicioAlmoco"];
								$out["distribuidores"][$pos]["fimAlmoco"]		 		= $distribuidores[$indexDistribuidor]['novoHorarioFuncionamento']["fimAlmoco"];
							}

							$out["distribuidores"][$pos]["distancia"] = calcDistancia($distribuidores[$indexDistribuidor]['taxaEntrega']["latitude"], $distribuidores[$indexDistribuidor]["longitude"], $endereco["latitude"], $endereco["longitude"]);;
							$out["distribuidores"][$pos]["taxaUnica"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["taxaUnica"];
							$out["distribuidores"][$pos]["valorTaxaUnica"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["valorTaxaUnica"];
							$out["distribuidores"][$pos]["taxaDomingo"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["taxaDomingo"];
							$out["distribuidores"][$pos]["valorTaxaDomingo"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["valorTaxaDomingo"];
							$out["distribuidores"][$pos]["taxaCompraMinima"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["taxaCompraMinima"];
							$out["distribuidores"][$pos]["valorCompraMinima"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["valorCompraMinima"];
							$out["distribuidores"][$pos]["taxaEntregaDistante"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["taxaEntregaDistante"];
							$out["distribuidores"][$pos]["distanciaMaxima"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["distanciaMaxima"];
							$out["distribuidores"][$pos]["valorKmAdicional"] = $distribuidores[$indexDistribuidor]['taxaEntrega']["valorKmAdicional"];

							$feriados = Feriado::where('idDistribuidor', $distribuidores[$indexDistribuidor]["id"])->get();

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

    function solicitaContato(Request $request){
		if($request->id==""){
			$clientePotencial = new Clientepotencial($request->all());
			$date = new DateTime();
			$clientePotencial->dataAcesso=$date->format('Y-m-d H:i:s');
		}else{
			$clientePotencial = ClientePotencial::find($request->id);
			$clientePotencial->fill($request->all());
		}

		$clientePotencial->status=ClientePotencial::SOLICITA_CONTATO;

		if($clientePotencial->save()){
			$out["msg"] =  "ok";
		}else{
			$out["msg"] =  "nok";
		}
		echo json_encode($out);
	}

    function enviaEmail(Request $request){
		$dados = $request->all();
		Mail::send('mail', $dados, function($m) use ($dados){
			$m->from('mailgun@mg.aguasterrasanta.com.br', $dados['nome']);
			$m->to('contato@tokumsede.com.br', 'Contato TôKumSede');
			$m->subject($dados['assunto']);
		});

		$out["msg"] = "ok";
		echo json_encode($out);
	}

    function removerEndereco(Request $request){

		$idEndereco = $request->id;
		$out["curPos"] = $request->curPos;
		$endereco = EnderecoCliente::find($idEndereco);

		if($endereco){

			if($endereco->atual){

				$novoAtual = EnderecoCliente::where([['idCliente',$endereco->idCliente],['atual', 0],['status',EnderecoCliente::ATIVO]])->first();

				if($novoAtual->count()){

					$novoAtual->update(['atual'=>true]);
					$out["novoAtual"] = $novoAtual->id;

				}else{
					$out["novoAtual"] = "nok";
				}

			}else{
				$out["novoAtual"] = "nok";
			}
			$endereco->update(['status'=>EnderecoCliente::EXCLUIDO, 'atual'=>false]);

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

    function consultaInicialSemCadastro(Request $request){

		$endereco["cidade"] = $request->cidade;
		$endereco["latitude"] = $request->latitude;
		$endereco["longitude"] = $request->longitude;

	$distrib = Distribuidor::with('enderecoDistribuidor:id,cidade,logradouro,estado,bairro,complemento,numero', 'novoHorarioFuncionamento')
		->where('status',Distribuidor::ATIVO)->get();

	foreach($distrib as $pos => $d){
		$out["distrib"][$pos]["nome"] 			= $d["nome"];
		$out["distrib"][$pos]["contato"] 		= "(".$d["dddTelefone"].") ".$d["telefonePrincipal"];
		$out["distrib"][$pos]["email"] 			= $d["email"];
		$out["distrib"][$pos]["logradouro"] 	= $d['enderecoDistribuidor']["logradouro"];
		$out["distrib"][$pos]["numero"] 		= $d['enderecoDistribuidor']["numero"];
		$out["distrib"][$pos]["complemento"]	= $d['enderecoDistribuidor']["complemento"];
		$out["distrib"][$pos]["bairro"] 		= $d['enderecoDistribuidor']["bairro"];
		$out["distrib"][$pos]["cidade"] 		= $d['enderecoDistribuidor']["cidade"];
		$out["distrib"][$pos]["estado"] 		= $d['enderecoDistribuidor']["estado"];

		$out["distrib"][$pos]["horario"][0]["funciona"] = (string)$d['novoHorarioFuncionamento']["domingo"];
		$out["distrib"][$pos]["horario"][0]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioDomingo"];
		$out["distrib"][$pos]["horario"][0]["fim"] 		= $d['novoHorarioFuncionamento']["fimDomingo"];
		$out["distrib"][$pos]["horario"][1]["funciona"] = (string)$d['novoHorarioFuncionamento']["segunda"];
		$out["distrib"][$pos]["horario"][1]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioSegunda"];
		$out["distrib"][$pos]["horario"][1]["fim"] 		= $d['novoHorarioFuncionamento']["fimSegunda"];
		$out["distrib"][$pos]["horario"][2]["funciona"] = (string)$d['novoHorarioFuncionamento']["terca"];
		$out["distrib"][$pos]["horario"][2]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioTerca"];
		$out["distrib"][$pos]["horario"][2]["fim"] 		= $d['novoHorarioFuncionamento']["fimTerca"];
		$out["distrib"][$pos]["horario"][3]["funciona"] = (string)$d['novoHorarioFuncionamento']["quarta"];
		$out["distrib"][$pos]["horario"][3]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioQuarta"];
		$out["distrib"][$pos]["horario"][3]["fim"] 		= $d['novoHorarioFuncionamento']["fimQuarta"];
		$out["distrib"][$pos]["horario"][4]["funciona"] = (string)$d['novoHorarioFuncionamento']["quinta"];
		$out["distrib"][$pos]["horario"][4]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioQuinta"];
		$out["distrib"][$pos]["horario"][4]["fim"] 		= $d['novoHorarioFuncionamento']["fimQuinta"];
		$out["distrib"][$pos]["horario"][5]["funciona"] = (string)$d['novoHorarioFuncionamento']["sexta"];
		$out["distrib"][$pos]["horario"][5]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioSexta"];
		$out["distrib"][$pos]["horario"][5]["fim"] 		= $d['novoHorarioFuncionamento']["fimSexta"];
		$out["distrib"][$pos]["horario"][6]["funciona"] = (string)$d['novoHorarioFuncionamento']["sabado"];
		$out["distrib"][$pos]["horario"][6]["inicio"] 	= $d['novoHorarioFuncionamento']["inicioSabado"];
		$out["distrib"][$pos]["horario"][6]["fim"] 		= $d['novoHorarioFuncionamento']["fimSabado"];
		$out["distrib"][$pos]["pausaAlmoco"] 			= (string)$d['novoHorarioFuncionamento']["pausaAlmoco"];
		$out["distrib"][$pos]["inicioAlmoco"] 			= $d['novoHorarioFuncionamento']["inicioAlmoco"];
		$out["distrib"][$pos]["fimAlmoco"]		 		= $d['novoHorarioFuncionamento']["fimAlmoco"];
	}

		$fator = 0.1; //Raio de aproximadamente 15,66 km

		$distribuidores = Distribuidor::selectRaw("distribuidor.*, enderecoDistribuidor.cidade as cidade, enderecoDistribuidor.latitude as latitude, enderecoDistribuidor.longitude as longitude, enderecoDistribuidor.cep as cep, enderecoDistribuidor.distanciaMaxima as distanciaMaxima")
			->Join("enderecoDistribuidor", 'enderecoDistribuidor.id', '=','distribuidor.idEnderecoDistribuidor')
			->Join("taxaEntrega", 'taxaEntrega.id', '=','distribuidor.idTaxaEntrega')
			->Join("horarioFuncionamento", 'horarioFuncionamento.id', '=','distribuidor.idHorarioFuncionamento')
			->whereRaw("distribuidor.status = ".Distribuidor::ATIVO." AND enderecoDistribuidor.latitude + $fator >= ".$endereco["latitude"]." AND enderecoDistribuidor.latitude - $fator <= ".$endereco["latitude"]." AND enderecoDistribuidor.longitude + $fator >= ".$endereco["longitude"]." AND enderecoDistribuidor.longitude - $fator <= ".$endereco["longitude"])
			->get();

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

				$idDistribuidor = $distribuidores[$indexDistribuidor]["tipoDistribuidor"]=="revendedor"?$distribuidores[$indexDistribuidor]["idDistribuidor"]:$distribuidores[$indexDistribuidor]["id"];

                $effectiveDistributorId = $this->getEffectiveDistributorId($idDistribuidor);

				$produtos = Preco::selectRaw("preco.*, produto.id as idProd, produto.nome as nome, produto.descricao as descricao, produto.img as img, categoria.nome as categoria, estoque.id as idEstoque")
					->leftJoin("produto", 'produto.id', '=', 'preco.idProduto')
					->leftJoin('categoria', 'categoria.id', '=', 'produto.idCategoria')
					->leftJoin('estoque', function($join) use ($effectiveDistributorId) {
                        $join->on('estoque.idProduto', '=', 'produto.id')
                        ->where('estoque.idDistribuidor', '=', $effectiveDistributorId);
                    })
					->whereRaw("preco.status = ".Preco::ATIVO." AND preco.idDistribuidor = ".$idDistribuidor. " AND estoque.quantidade >= 1 ".
					" AND ((preco.inicioValidade IS NULL OR preco.inicioValidade <= CURDATE()) AND (preco.fimValidade IS NULL OR preco.fimValidade >= CURDATE())) ".
					" AND ((preco.inicioHora IS NULL OR preco.inicioHora <= CURTIME()) AND (preco.fimHora IS NULL OR preco.fimHora > CURTIME())) AND preco.idCliente IS NULL")
					->orderByRaw("categoria.nome ASC, produto.nome, preco.qtdMin ASC")
					->get();

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

	function clientePotencial(Request $request){

		$clientePotencial = new ClientePotencial($request->all());
		$date = new DateTime();
		$clientePotencial->dataAcesso	=	$date->format('Y-m-d H:i:s');
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

	function refreshRegId(Request $request){

		$cliente = Cliente::find($request->idCliente);

		if($cliente){

			$cliente->regId = $request->regId;
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

    function notificacaoRecebida(Request $request){
		$pedido = Pedido::find($request->idPedido);
		if($pedido){
			$pedido->update(['statusChange'=>0]);
		}
	}

    function senhaModoTeste(Request $request){

		if($request->senha=="tokumsedes3cr3t"){
			$out["msg"] = "ok";
		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);
	}

    function alteraEnderecoAtual(Request $request){

		EnderecoCliente::where('idCliente',$request->idCliente)->update(['atual'=>'0']);

		EnderecoCliente::where('id',$request->idEndereco)->update(['atual'=>'1']);

		$out["msg"]	= "ok";
		echo json_encode($out);

	}

    function cadastrarNovoEndereco(Request $request){

		$enderecoCliente = new Enderecocliente($request->all());
		$enderecoCliente->logradouro	=   $request->endereco;
		$enderecoCliente->atual			=   true;
		$enderecoCliente->status		=	Enderecocliente::ATIVO;

		if($enderecoCliente->save()){

			EnderecoCliente::where([['idCliente',$enderecoCliente->idCliente],['id','!=',$enderecoCliente->id]])->update(['atual'=>'0']);

			// $request->replace([
			//  'idCliente' => $enderecoCliente->idCliente,//Enviar vários parâmetros
			// ]);
			$requestIdCliente = new Request(['idCliente' => $enderecoCliente->idCliente]);

			$this->consultaInicial($requestIdCliente);

		}else{
			$out["msg"] = "nok";
			echo json_encode($out);
		}
	}

    function cancelarPedido(Request $request){

		$pedido = Pedido::find($request->idPedido);

		if($pedido){

			if($pedido->update(['status'=>Pedido::CANCELADO_USUARIO])){
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok";
			}

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}

    function pedidoRecebido(Request $request){

		$idPedido = $request->idPedido;
		$pedido = Pedido::find($idPedido);
		$date = new DateTime();
		// //Buscar distribuidor e cliente ***PREMIAÇÕES
		// if($premiacoes){
		// 	$distribuidor = Distribuidor::find($pedido->idDistribuidor);
		// 	$enderecoCliente = EnderecoCliente::find($pedido->idEndereco);
		// 	$cliente = Cliente::find($enderecoCliente->idCliente);
		// }
		if($pedido){

			//ALTERA OS ESTOQUES
	        if($pedido->status == Pedido::ACEITO){
				$itensPedido = ItemPedido::where('idPedido', $idPedido)->get();

	            for ($i = 0; $i < sizeof($itensPedido); $i++) {
	                $estoques = Estoque::where([['idDistribuidor',$pedido->idDistribuidor],['idProduto',$itensPedido[$i]["idProduto"]]])->get();
	                $estoque = Estoque::find($estoques[0]["id"]);
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

    function alteraDadosCliente(Request $request){
		$cliente = Cliente::find($request->id);

		if($cliente){
			$request['senha'] = $request->senha == '' ? $cliente->senha : $request->senha;

			if($cliente->update($request->all())){
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok";
			}

		}else{
			$out["msg"] = "nok";
		}

		echo json_encode($out);

	}

    function verifyRecover(Request $request){

		$cliente = Cliente::where([['email',$request->email],['dddTelefone',$request->ddd],['telefone',$request->telefone],['status',Cliente::ATIVO]])->get();
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

    function alteraSenha(Request $request){

		$cliente = Cliente::find($request->idCliente);

		if($cliente){

			if($cliente->update(['senha'=>$request->senha])){

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

    function novoPedido(Request $request){

		$pedido = new Pedido($request->all());
		$pedido->horarioPedido 	= now()->format('Y-m-d H:i:s');
		$pedido->status 		= Pedido::PENDENTE;
		$pedido->statusChange	= 0;
		$pedido->trocoPara      = $pedido->trocoPara!=""?str_replace(",",".",$request->trocoPara):0;
		$pedido->total      	= str_replace(",",".",$request->totalPedido);
		$pedido->obs 			= $request->testePh?'FAZER TESTE DE PH. '.$request->obs:$request->obs;

		$produtos = rawurldecode($request->produtos);
		$produtos = json_decode($produtos);
		$distribuidor = Distribuidor::find($pedido->idDistribuidor);
		$endereco = EnderecoCliente::find($pedido->idEndereco);
		$cliente = Cliente::find($endereco->idCliente);
		if($distribuidor->tipoDistribuidor=="revendedor"){
			//$prodRevendedor = array(1, 4, 5, 6, 332, 333, 335); //ID dos produtos do revendedor
			foreach ($produtos as $produto) {
				if ($produto->produto->id != 1 && $produto->produto->id != 4 && $produto->produto->id != 5 && $produto->produto->id != 6 && $produto->produto->id != 332 && $produto->produto->id != 333 && $produto->produto->id != 335) {//prodRevendedor.indexOf(parseInt($produto->produto->id)) == -1
					$pedido->idDistribuidor = $distribuidor->idDistribuidor;
				}
			}
		}
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
			$administradores = Administrador::where('status','Ativo')
				->whereRaw("tipoAdministrador = 'Administrador' or tipoAdministrador = 'Atendente' or idDistribuidor =".$pedido->idDistribuidor)
				->get();

            // API access key from Google API's Console
            $texto = $endereco->logradouro.' '.$endereco->numero.', '.$endereco->bairro.' - '.$endereco->cidade.'/'.$endereco->estado;
            // prep the bundle
            $msg = array(
                'title' => 'Pedido '.$pedido->id.' - '.$cliente->nome,
                'body' => $texto,
                'tag' => $pedido->id,
                'icon' => '/images/logo-icon.png',
                'click_action' => 'https://adm.tokumsede.com'
            );
            $headers = array(
                'Authorization: key=AAAA92nZhZY:APA91bFbwC0HrbjmBGjQIrXtPrPZcH5gmCFK9y1jlQucH03VlNOHlO45Ru5Dk69iplWGYcnsVUbhG2hMH5AgoZzU9GCK0DmFplBjLz-QAmlFM5YOpmFFOr5ak--7l-yLahiaJKPPIUct',
                'Content-Type: application/json'
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
    $key = "AIzaSyD3A65oIloNfr-TA3EK8vERo2nnWEi1fxg";
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
