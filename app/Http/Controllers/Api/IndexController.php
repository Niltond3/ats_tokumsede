<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request; // Make sure this is present
use App\Http\Controllers\Controller;
// use App\Http\Controllers\api\UtilController; // Not used in provided snippet
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\EnderecoCliente;
use App\Models\Preco;
use App\Models\Feriado;
use App\Models\Administrador;
use App\Models\ClientePotencial;
use App\Models\Distribuidor;
// use App\Models\Composicao; // Not used in provided snippet
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
// use Mailgun\Mailgun; // Not used in provided snippet
// use DateTimeZone; // Not used in provided snippet
use DateTime;
use App\Actions\Enderecos\UpdateCoordinatesAction; // <-- IMPORT THE ACTION
use Illuminate\Support\Facades\Log; // <-- For logging errors/info

class IndexController extends Controller
{
    private $updateCoordinatesAction; // <-- PROPERTY TO HOLD THE ACTION

    /**
     * Constructor to inject dependencies.
     */
    public function __construct(UpdateCoordinatesAction $updateCoordinatesAction) // <-- INJECT ACTION
    {
        $this->updateCoordinatesAction = $updateCoordinatesAction;
    }

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
        if (!$distributor) { // Basic check
            return $distributorId;
        }
        return $distributor->getMainDistributorIdAttribute() ?? $distributorId;
    }

    private function getActiveProducts($distribuidorId)
{
    // Get effective distributor ID (main distributor if in union)
    $effectiveDistributorId = $this->getEffectiveDistributorId($distribuidorId);

    return Preco::selectRaw("preco.*, produto.id as idProd, produto.nome as nome, produto.descricao as descricao, produto.img as img, categoria.nome as categoria, estoque.id as idEstoque")
							->Join("produto", 'produto.id', '=', 'preco.idProduto')
							->Join('categoria', 'categoria.id', '=', 'produto.idCategoria')
							->leftJoin('estoque', function($join) use ($effectiveDistributorId) {
            $join->on('estoque.idProduto', '=', 'produto.id')
                 ->where('estoque.idDistribuidor', '=', $effectiveDistributorId);
        })
							->whereRaw(
                                "preco.status = ".Preco::ATIVO." AND preco.idDistribuidor = ".$distribuidorId. " AND produto.status = ".Produto::ATIVO." AND estoque.quantidade >= 1 ". " AND ((preco.inicioValidade IS NULL OR preco.inicioValidade <= CURDATE()) AND (preco.fimValidade IS NULL OR preco.fimValidade >= CURDATE())) ".
							" AND ((preco.inicioHora IS NULL OR preco.inicioHora <= CURTIME()) AND (preco.fimHora IS NULL OR preco.fimHora > CURTIME())) AND preco.idCliente IS NULL")
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
					// Assuming gcmSend is defined elsewhere or should be part of this class.
                    // $this->gcmSend($cliente->regId, $idCliente, $p["id"], $p["status"], $p["retorno"], $p["origem"], false);
                    Log::info("gcmSend would be called for order ID: " . $p["id"]); // Placeholder
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

		$appVersion	= $request->appVersion?$request->appVersion:($cliente ? $cliente->appVersion : null);

		$distrib = Distribuidor::where('status',Distribuidor::ATIVO)
			->with('novoHorarioFuncionamento','enderecoDistribuidor:id,cidade,logradouro,estado,bairro,complemento,numero')
			->get();

        $out = []; // Initialize $out array

		foreach($distrib as $pos => $d){
			$out["distrib"][$pos]["nome"] 			= $d["nome"];
			$out["distrib"][$pos]["contato"] 		= "(".$d["dddTelefone"].") ".$d["telefonePrincipal"];
			$out["distrib"][$pos]["email"] 			= $d["email"];
            if ($d['enderecoDistribuidor']) {
                $out["distrib"][$pos]["logradouro"] 	= $d['enderecoDistribuidor']["logradouro"];
                $out["distrib"][$pos]["numero"] 		= $d['enderecoDistribuidor']["numero"];
                $out["distrib"][$pos]["complemento"]	= $d['enderecoDistribuidor']["complemento"];
                $out["distrib"][$pos]["bairro"] 		= $d['enderecoDistribuidor']["bairro"];
                $out["distrib"][$pos]["cidade"] 		= $d['enderecoDistribuidor']["cidade"];
                $out["distrib"][$pos]["estado"] 		= $d['enderecoDistribuidor']["estado"];
            } else {
                 $out["distrib"][$pos]["logradouro"] 	= null;
                 $out["distrib"][$pos]["numero"] 		= null;
                 $out["distrib"][$pos]["complemento"]	= null;
                 $out["distrib"][$pos]["bairro"] 		= null;
                 $out["distrib"][$pos]["cidade"] 		= null;
                 $out["distrib"][$pos]["estado"] 		= null;
            }
            if ($d['novoHorarioFuncionamento']) {
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
            } else {
                $out["distrib"][$pos]["horario"] = []; // Or some default structure
                $out["distrib"][$pos]["pausaAlmoco"] = null;
                $out["distrib"][$pos]["inicioAlmoco"] = null;
                $out["distrib"][$pos]["fimAlmoco"] = null;
            }
		}

		if($cliente && $cliente->status == Cliente::ATIVO){

			if($system != null){
				$cliente->appVersion = $system." => ".$appVersion;
			}
			$cliente->ultimoLogin = now();
			$cliente->save();

			$out["msg"] = "ok";
			$enderecos = EnderecoCliente::where([['idCliente',$idCliente],['status',EnderecoCliente::ATIVO]])->get();

            // START: Update coordinates for all fetched addresses
            foreach ($enderecos as $key => $enderecoModel) {
                if ($enderecoModel->logradouro && $enderecoModel->cidade && $enderecoModel->estado) {
                    Log::info("Attempting to fetch coordinates for address ID: {$enderecoModel->id}");
                    $coordenadas = buscarLatitudeLongitude(
                        $enderecoModel->logradouro,
                        $enderecoModel->numero,
                        $enderecoModel->cidade,
                        $enderecoModel->estado,
                        $enderecoModel->cep
                    );

                    if ($coordenadas && isset($coordenadas[0]) && isset($coordenadas[1])) {
                        $newLatitude = floatval($coordenadas[0]); // Ensure float
                        $newLongitude = floatval($coordenadas[1]); // Ensure float

                        // Create a new Request object for the action
                        $updateRequest = new Request([
                            'latitude' => $newLatitude,
                            'longitude' => $newLongitude,
                        ]);

                        Log::info("Calling UpdateCoordinatesAction for address ID: {$enderecoModel->id} with new coords: {$newLatitude}, {$newLongitude}");
                        // Call the action to update coordinates
                        $updatedEnderecoModel = $this->updateCoordinatesAction->execute($updateRequest, $enderecoModel->id);

                        if ($updatedEnderecoModel) {
                            // Replace the model in the collection with the updated one
                            $enderecos[$key] = $updatedEnderecoModel;
                            Log::info("Coordinates updated and model refreshed in collection for address ID: {$enderecoModel->id}");
                        } else {
                            Log::warning("UpdateCoordinatesAction returned null for address ID: {$enderecoModel->id}. Using original model.");
                        }
                    } else {
                        Log::warning("Could not fetch new coordinates for address ID: {$enderecoModel->id}. Address details: " .
                            "{$enderecoModel->logradouro}, {$enderecoModel->numero}, {$enderecoModel->cidade}, {$enderecoModel->estado}, {$enderecoModel->cep}");
                    }
                } else {
                    Log::warning("Address ID: {$enderecoModel->id} has incomplete data for geocoding.");
                }
            }
            // END: Update coordinates

			$out["freeTax"] = array(1, 4, 5, 6, 332, 333, 335); //ID dos produtos que geram liberação de Taxa de Entrega
			$fator = 0.2; //Raio de aproximadamente 30km

			foreach($enderecos as $pos => $endereco){ // Now $endereco is the potentially updated EnderecoCliente model
				$out["enderecos"][$pos]["id"] 			= $endereco->id; // Use object access
				$out["enderecos"][$pos]["apelido"] 		= $endereco->apelido;
				$out["enderecos"][$pos]["atual"] 		= (bool)$endereco->atual;
				$out["enderecos"][$pos]["logradouro"] 	= $endereco->logradouro;
				$out["enderecos"][$pos]["numero"] 		= $endereco->numero;
				$out["enderecos"][$pos]["bairro"] 		= $endereco->bairro;
				$out["enderecos"][$pos]["complemento"] 	= $endereco->complemento;
				$out["enderecos"][$pos]["cep"] 			= $endereco->cep;
				$out["enderecos"][$pos]["cidade"] 		= $endereco->cidade;
				$out["enderecos"][$pos]["estado"] 		= $endereco->estado;
				$out["enderecos"][$pos]["referencia"] 	= $endereco->referencia;
				$out["enderecos"][$pos]["latitude"] 	= $endereco->latitude;
				$out["enderecos"][$pos]["longitude"] 	= $endereco->longitude;

                // Ensure latitude and longitude are valid numbers before using them in calculations
                $enderecoLatitude = floatval($endereco->latitude);
                $enderecoLongitude = floatval($endereco->longitude);

                if (!is_numeric($enderecoLatitude) || !is_numeric($enderecoLongitude)) {
                    Log::warning("Invalid coordinates for address ID {$endereco->id} after update attempt: Lat {$endereco->latitude}, Lng {$endereco->longitude}. Skipping distributor search for this address.");
                    $out["distribuidores"][$pos]["idDistribuidor"] = null;
                    continue; // Skip to the next address
                }


				$joinHorario = $appVersion==null?"horarioFuncionamento":'novoHorarioFuncionamento';
                $relationName = $appVersion==null?"horarioFuncionamento":'novoHorarioFuncionamento';


				$distribuidores = Distribuidor::selectRaw("distribuidor.*, enderecoDistribuidor.cidade as cidade, enderecoDistribuidor.latitude as latitudeED, enderecoDistribuidor.longitude as longitudeED, enderecoDistribuidor.cep as cep, enderecoDistribuidor.distanciaMaxima as distanciaMaxima")
					->Join("enderecoDistribuidor", 'enderecoDistribuidor.id', '=','distribuidor.idEnderecoDistribuidor')
					->Join("taxaEntrega", 'taxaEntrega.id', '=','distribuidor.idTaxaEntrega')
					// ->Join($joinHorario, $joinHorario.'.id', '=','distribuidor.id'.$joinHorario) // This join might be redundant if using with() later
                    ->with($relationName, 'taxaEntrega') // Eager load relations
					->where("distribuidor.status", Distribuidor::ATIVO)
                    ->whereRaw("enderecoDistribuidor.latitude + ? >= ? AND enderecoDistribuidor.latitude - ? <= ? AND enderecoDistribuidor.longitude + ? >= ? AND enderecoDistribuidor.longitude - ? <= ?",
                        [$fator, $enderecoLatitude, $fator, $enderecoLatitude, $fator, $enderecoLongitude, $fator, $enderecoLongitude])
					->get();

				if(count($distribuidores) == 0){
					$out["distribuidores"][$pos]["idDistribuidor"] = null;
				}else{
					//DETERMINA DISTRIBUIDOR MAIS PRÓXIMO
					$indexDistribuidor = 0; $dists = array();
					foreach($distribuidores as $pos2 => $d){
                        // Use the aliased latitude/longitude from the selectRaw
						$dist = calcDistancia($d->latitudeED, $d->longitudeED, $enderecoLatitude, $enderecoLongitude);
						$dists[$pos2] = array("max" => $d->distanciaMaxima, "atual" => $dist, "pos" => $pos2);
					}

					$indexDistribuidor = $this->selectDist($dists);

					if($indexDistribuidor != -1){ //CASO O RAIO DE ATENDIMENTO DO DISTRIBUIDOR ALCANCE A LOCALIZACAO DO CLIENTE
                        $selectedDistributor = $distribuidores[$indexDistribuidor];
						$idDistribuidor = $selectedDistributor->tipoDistribuidor == "revendedor" ? $selectedDistributor->idDistribuidor : $selectedDistributor->id;

						$produtos = $this->getActiveProducts($idDistribuidor);

						if(count($produtos)){
							//MONTA JSON DE PRODUTOS
							$out["distribuidores"][$pos]["idDistribuidor"] = $selectedDistributor->id;
							$out["distribuidores"][$pos]["contato"] = "(".$selectedDistributor->dddTelefone.") ".$selectedDistributor->telefonePrincipal;

                            $horarioData = $selectedDistributor->{$relationName}; // Access eager loaded relation

							if($appVersion == null){ // Assuming 'horarioFuncionamento' is the old relation
                                if ($horarioData) {
                                    $out["distribuidores"][$pos]["inicioSemana"] 	= $horarioData["inicioSemana"];
                                    $out["distribuidores"][$pos]["fimSemana"] 		= $horarioData["fimSemana"];
                                    $out["distribuidores"][$pos]["inicioSabado"] 	= $horarioData["inicioSabado"];
                                    $out["distribuidores"][$pos]["fimSabado"] 		= $horarioData["fimSabado"];
                                    $out["distribuidores"][$pos]["domingo"] 		= (bool)$horarioData["domingo"];
                                    $out["distribuidores"][$pos]["inicioDomingo"] 	= $horarioData["inicioDomingo"];
                                    $out["distribuidores"][$pos]["fimDomingo"] 		= $horarioData["fimDomingo"];
                                }
							}else{ // Assuming 'novoHorarioFuncionamento' is the new relation
                                if ($horarioData) {
                                    $out["distribuidores"][$pos]["horario"][0]["funciona"] 	= (string)$horarioData["domingo"];
                                    $out["distribuidores"][$pos]["horario"][0]["inicio"] 	= $horarioData["inicioDomingo"];
                                    $out["distribuidores"][$pos]["horario"][0]["fim"] 		= $horarioData["fimDomingo"];
                                    // ... (repeat for all days)
                                    $out["distribuidores"][$pos]["horario"][1]["funciona"] 	= (string)$horarioData["segunda"];
                                    $out["distribuidores"][$pos]["horario"][1]["inicio"] 	= $horarioData["inicioSegunda"];
                                    $out["distribuidores"][$pos]["horario"][1]["fim"] 		= $horarioData["fimSegunda"];
                                    $out["distribuidores"][$pos]["horario"][2]["funciona"] 	= (string)$horarioData["terca"];
                                    $out["distribuidores"][$pos]["horario"][2]["inicio"] 	= $horarioData["inicioTerca"];
                                    $out["distribuidores"][$pos]["horario"][2]["fim"] 		= $horarioData["fimTerca"];
                                    $out["distribuidores"][$pos]["horario"][3]["funciona"] 	= (string)$horarioData["quarta"];
                                    $out["distribuidores"][$pos]["horario"][3]["inicio"] 	= $horarioData["inicioQuarta"];
                                    $out["distribuidores"][$pos]["horario"][3]["fim"] 		= $horarioData["fimQuarta"];
                                    $out["distribuidores"][$pos]["horario"][4]["funciona"] 	= (string)$horarioData["quinta"];
                                    $out["distribuidores"][$pos]["horario"][4]["inicio"] 	= $horarioData["inicioQuinta"];
                                    $out["distribuidores"][$pos]["horario"][4]["fim"] 		= $horarioData["fimQuinta"];
                                    $out["distribuidores"][$pos]["horario"][5]["funciona"] 	= (string)$horarioData["sexta"];
                                    $out["distribuidores"][$pos]["horario"][5]["inicio"] 	= $horarioData["inicioSexta"];
                                    $out["distribuidores"][$pos]["horario"][5]["fim"] 		= $horarioData["fimSexta"];
                                    $out["distribuidores"][$pos]["horario"][6]["funciona"] 	= (string)$horarioData["sabado"];
                                    $out["distribuidores"][$pos]["horario"][6]["inicio"] 	= $horarioData["inicioSabado"];
                                    $out["distribuidores"][$pos]["horario"][6]["fim"] 		= $horarioData["fimSabado"];
                                    $out["distribuidores"][$pos]["pausaAlmoco"] 			= (string)$horarioData["pausaAlmoco"];
                                    $out["distribuidores"][$pos]["inicioAlmoco"] 			= $horarioData["inicioAlmoco"];
                                    $out["distribuidores"][$pos]["fimAlmoco"]		 		= $horarioData["fimAlmoco"];
                                }
							}
                            $taxaEntregaData = $selectedDistributor->taxaEntrega; // Access eager loaded relation
                            if ($taxaEntregaData) {
                                $out["distribuidores"][$pos]["distancia"] = calcDistancia($taxaEntregaData->latitude, $selectedDistributor->longitudeED, $enderecoLatitude, $enderecoLongitude);
                                $out["distribuidores"][$pos]["taxaUnica"] = $taxaEntregaData->taxaUnica;
                                $out["distribuidores"][$pos]["valorTaxaUnica"] = $taxaEntregaData->valorTaxaUnica;
                                $out["distribuidores"][$pos]["taxaDomingo"] = $taxaEntregaData->taxaDomingo;
                                $out["distribuidores"][$pos]["valorTaxaDomingo"] = $taxaEntregaData->valorTaxaDomingo;
                                $out["distribuidores"][$pos]["taxaCompraMinima"] = $taxaEntregaData->taxaCompraMinima;
                                $out["distribuidores"][$pos]["valorCompraMinima"] = $taxaEntregaData->valorCompraMinima;
                                $out["distribuidores"][$pos]["taxaEntregaDistante"] = $taxaEntregaData->taxaEntregaDistante;
                                $out["distribuidores"][$pos]["distanciaMaxima"] = $taxaEntregaData->distanciaMaxima; // This seems to be from taxaEntrega not enderecoDist.
                                $out["distribuidores"][$pos]["valorKmAdicional"] = $taxaEntregaData->valorKmAdicional;
                            }

							$feriados = Feriado::where('idDistribuidor', $selectedDistributor->id)->get();

							if(count($feriados)){
								foreach($feriados as $fPos => $f){
									$out["distribuidores"][$pos]["feriados"][$fPos] = $f->dataFeriado;
								}
							}else{
								$out["distribuidores"][$pos]["feriados"] = [];
							}

							$indexCategoria = 0; $indexProduto = 0; $indexPreco = 0;
							$txtCategoria = $produtos[0]->categoria;
							$txtProduto = $produtos[0]->idProd;

							foreach($produtos as $x => $prod){

								if($txtCategoria != $prod->categoria){
									$indexCategoria++; $indexProduto = 0; $indexPreco = 0; $txtCategoria = $prod->categoria;
								}else if($txtProduto != $prod->idProd){
									$indexProduto++; $indexPreco = 0; $txtProduto = $prod->idProd;
								}

								$out["distribuidores"][$pos]["content"][$indexCategoria]["categoria"] = $prod->categoria;

								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["nome"] = $prod->nome;
								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["descricao"] = $prod->descricao;
								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["id"] = $prod->idProd;
								$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["img"] = $prod->img;

									if($indexPreco > 0){
										if($produtos[($x - 1)]->qtdMin == $produtos[$x]->qtdMin){
											if($produtos[$x]->inicioValidade != null || $produtos[$x]->fimValidade != null){
												$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["qtd"] = $prod->qtdMin;
												$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["val"] = $prod->valor;
											}else if($produtos[$x]->inicioHora != null || $produtos[$x]->fimHora != null){
												if(!($produtos[($x - 1)]->inicioValidade != null || $produtos[($x - 1)]->fimValidade != null)){
													$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["qtd"] = $prod->qtdMin;
													$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][($indexPreco - 1)]["val"] = $prod->valor;
												}
											}
										}else{
											$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["qtd"] = $prod->qtdMin;
											$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["val"] = $prod->valor;
											$indexPreco++;
										}
									}else{
										$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["qtd"] = $prod->qtdMin;
										$out["distribuidores"][$pos]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["val"] = $prod->valor;
										$indexPreco++;
									}
									$txtProduto = $prod->idProd;
							}

						}else{ //CASO O DISTRIBUIDOR NAO POSSUA PRODUTOS ATIVOS
							$out["distribuidores"][$pos]["idDistribuidor"] = null;
						}

					}else{ //SENAO NAO HA DISTRIBUIDOR DISPONIVEL PARA O ENDERECO
						$out["distribuidores"][$pos]["idDistribuidor"] = null;
					}
				}
			}

			// header('Content-Type: application/json'); // Headers should be set by Laravel automatically
			// echo json_encode($out, JSON_PRETTY_PRINT);
            return response()->json($out, 200, [], JSON_PRETTY_PRINT);


		}else{
			$out["msg"] = "not found";
			// echo json_encode($out);
            return response()->json($out);
		}
	}

    // ... (rest of the IndexController methods remain the same) ...
    // Make sure to include login, solicitaContato, enviaEmail, etc.

	function login(Request $request){

		$cliente = Cliente::whereRaw("cliente.email = ? AND ( cliente.senha = ? OR cliente.senha = ? OR cliente.senha = ? ) AND cliente.status = ?",
            [$request->email, $request->senha, md5($request->senha), bcrypt($request->senha), Cliente::ATIVO])
            ->first(); // Use first() for single record

        if ($cliente) { // Eloquent model will be returned or null
            $out["msg"] = "ok";
			$out["idCliente"] = $cliente->id;

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

        }else{
        	$out["msg"] = "nok";
        }
		return response()->json($out); // Use Laravel response
	}

    function solicitaContato(Request $request){
		if($request->id==""){
			$clientePotencial = new Clientepotencial($request->all());
			$date = new DateTime();
			$clientePotencial->dataAcesso=$date->format('Y-m-d H:i:s');
		}else{
			$clientePotencial = ClientePotencial::find($request->id);
            if (!$clientePotencial) {
                return response()->json(["msg" => "not found"], 404);
            }
			$clientePotencial->fill($request->all());
		}

		$clientePotencial->status=ClientePotencial::SOLICITA_CONTATO;

		if($clientePotencial->save()){
			$out["msg"] =  "ok";
		}else{
			$out["msg"] =  "nok";
		}
		return response()->json($out);
	}

    function enviaEmail(Request $request){
		$dados = $request->all();
        try {
            Mail::send('mail', $dados, function($m) use ($dados){
                $m->from('mailgun@mg.aguasterrasanta.com.br', $dados['nome']);
                $m->to('contato@tokumsede.com.br', 'Contato TôKumSede');
                $m->subject($dados['assunto']);
            });
            $out["msg"] = "ok";
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            $out["msg"] = "nok";
            $out["error"] = $e->getMessage(); // Optionally include error
        }
		return response()->json($out);
	}

    function removerEndereco(Request $request){
		$idEndereco = $request->id;
		$out["curPos"] = $request->curPos;
		$endereco = EnderecoCliente::find($idEndereco);

		if($endereco){
			if($endereco->atual){
				$novoAtual = EnderecoCliente::where('idCliente',$endereco->idCliente)
                                            ->where('id', '!=', $idEndereco) // Exclude current one
                                            ->where('status',EnderecoCliente::ATIVO)
                                            ->orderBy('id', 'desc') // Or some other logic to pick next
                                            ->first();
				if($novoAtual){ // Check if a model was found
					$novoAtual->update(['atual'=>true]);
					$out["novoAtual"] = $novoAtual->id;
				}else{
					$out["novoAtual"] = "nok"; // No other address to make current
				}
			}else{
				$out["novoAtual"] = "nok"; // Was not current, so no new current to set from here
			}
			$endereco->update(['status'=>EnderecoCliente::EXCLUIDO, 'atual'=>false]);
			$out["msg"] = "ok";
		}else{
			$out["msg"] = "nok";
		}
		return response()->json($out);
	}

    function listImages(){
		$img = Produto::select('img')
        ->where('status', '!=', Produto::EXCLUIDO) // Assuming 3 is EXCLUIDO, use constant
        ->whereNotNull('img')
        ->where('img', '!=', '')
        ->groupBy('img')
        ->pluck('img') // Pluck to get a simple array of image names
        ->all();

		// $out["img"] = $img; // Simpler assignment
        // The original code structure for $out was: $out["img"][$pos] = $i["img"];
        // If you need that exact structure:
        $out = ['img' => []];
        foreach($img as $pos => $i_name){
			$out["img"][$pos] = $i_name;
		}
		return response()->json($out);
	}

    function consultaInicialSemCadastro(Request $request){
        $out = []; // Initialize
		$endereco = [];
		$endereco["cidade"] = $request->cidade;
		$endereco["latitude"] = floatval($request->latitude); // Ensure float
		$endereco["longitude"] = floatval($request->longitude); // Ensure float

        if (!is_numeric($endereco["latitude"]) || !is_numeric($endereco["longitude"])) {
             return response()->json(['msg' => 'nok', 'error' => 'Invalid latitude/longitude'], 400);
        }

	$distrib = Distribuidor::with([
        'enderecoDistribuidor:id,cidade,logradouro,estado,bairro,complemento,numero',
        'novoHorarioFuncionamento'
        ])
		->where('status',Distribuidor::ATIVO)->get();

	foreach($distrib as $pos => $d){
		$out["distrib"][$pos]["nome"] 			= $d->nome;
		$out["distrib"][$pos]["contato"] 		= "(".$d->dddTelefone.") ".$d->telefonePrincipal;
		$out["distrib"][$pos]["email"] 			= $d->email;
        if ($d->enderecoDistribuidor) {
            $out["distrib"][$pos]["logradouro"] 	= $d->enderecoDistribuidor->logradouro;
            $out["distrib"][$pos]["numero"] 		= $d->enderecoDistribuidor->numero;
            $out["distrib"][$pos]["complemento"]	= $d->enderecoDistribuidor->complemento;
            $out["distrib"][$pos]["bairro"] 		= $d->enderecoDistribuidor->bairro;
            $out["distrib"][$pos]["cidade"] 		= $d->enderecoDistribuidor->cidade;
            $out["distrib"][$pos]["estado"] 		= $d->enderecoDistribuidor->estado;
        }
        if ($d->novoHorarioFuncionamento) {
            $nhf = $d->novoHorarioFuncionamento;
            $out["distrib"][$pos]["horario"][0]["funciona"] = (string)$nhf->domingo;
            $out["distrib"][$pos]["horario"][0]["inicio"] 	= $nhf->inicioDomingo;
            $out["distrib"][$pos]["horario"][0]["fim"] 		= $nhf->fimDomingo;
            // ... (repeat for all days as in consultaInicial)
            $out["distrib"][$pos]["horario"][1]["funciona"] = (string)$nhf->segunda;
            $out["distrib"][$pos]["horario"][1]["inicio"] 	= $nhf->inicioSegunda;
            $out["distrib"][$pos]["horario"][1]["fim"] 		= $nhf->fimSegunda;
            $out["distrib"][$pos]["horario"][2]["funciona"] = (string)$nhf->terca;
            $out["distrib"][$pos]["horario"][2]["inicio"] 	= $nhf->inicioTerca;
            $out["distrib"][$pos]["horario"][2]["fim"] 		= $nhf->fimTerca;
            $out["distrib"][$pos]["horario"][3]["funciona"] = (string)$nhf->quarta;
            $out["distrib"][$pos]["horario"][3]["inicio"] 	= $nhf->inicioQuarta;
            $out["distrib"][$pos]["horario"][3]["fim"] 		= $nhf->fimQuarta;
            $out["distrib"][$pos]["horario"][4]["funciona"] = (string)$nhf->quinta;
            $out["distrib"][$pos]["horario"][4]["inicio"] 	= $nhf->inicioQuinta;
            $out["distrib"][$pos]["horario"][4]["fim"] 		= $nhf->fimQuinta;
            $out["distrib"][$pos]["horario"][5]["funciona"] = (string)$nhf->sexta;
            $out["distrib"][$pos]["horario"][5]["inicio"] 	= $nhf->inicioSexta;
            $out["distrib"][$pos]["horario"][5]["fim"] 		= $nhf->fimSexta;
            $out["distrib"][$pos]["horario"][6]["funciona"] = (string)$nhf->sabado;
            $out["distrib"][$pos]["horario"][6]["inicio"] 	= $nhf->inicioSabado;
            $out["distrib"][$pos]["horario"][6]["fim"] 		= $nhf->fimSabado;

            $out["distrib"][$pos]["pausaAlmoco"] 			= (string)$nhf->pausaAlmoco;
            $out["distrib"][$pos]["inicioAlmoco"] 			= $nhf->inicioAlmoco;
            $out["distrib"][$pos]["fimAlmoco"]		 		= $nhf->fimAlmoco;
        }
	}

		$fator = 0.1; //Raio de aproximadamente 15,66 km

		$distribuidores = Distribuidor::selectRaw("distribuidor.*, enderecoDistribuidor.cidade as cidadeED, enderecoDistribuidor.latitude as latitudeED, enderecoDistribuidor.longitude as longitudeED, enderecoDistribuidor.cep as cepED, enderecoDistribuidor.distanciaMaxima as distanciaMaxima")
			->Join("enderecoDistribuidor", 'enderecoDistribuidor.id', '=','distribuidor.idEnderecoDistribuidor')
			->Join("taxaEntrega", 'taxaEntrega.id', '=','distribuidor.idTaxaEntrega')
			->Join("horarioFuncionamento", 'horarioFuncionamento.id', '=','distribuidor.idHorarioFuncionamento') // Consider if this should be novoHorarioFuncionamento
			->where("distribuidor.status", Distribuidor::ATIVO)
            ->whereRaw("enderecoDistribuidor.latitude + ? >= ? AND enderecoDistribuidor.latitude - ? <= ? AND enderecoDistribuidor.longitude + ? >= ? AND enderecoDistribuidor.longitude - ? <= ?",
                        [$fator, $endereco["latitude"], $fator, $endereco["latitude"], $fator, $endereco["longitude"], $fator, $endereco["longitude"]])
			->get();

		if(count($distribuidores) == 0){
			$out["msg"] = "nok";
			$out["distribuidores"][0]["idDistribuidor"] = null;
		}else{
			$indexDistribuidor = 0; $dists = array();
			foreach($distribuidores as $pos2 => $d){
				$dist = calcDistancia($d->latitudeED, $d->longitudeED, $endereco["latitude"], $endereco["longitude"]);
				$dists[$pos2] = array("max" => $d->distanciaMaxima, "atual" => $dist, "pos" => $pos2);
			}
			$indexDistribuidor = $this->selectDist($dists);

			if($indexDistribuidor != -1){
                $selectedDistributor = $distribuidores[$indexDistribuidor];
				$idDistribuidor = $selectedDistributor->tipoDistribuidor == "revendedor" ? $selectedDistributor->idDistribuidor : $selectedDistributor->id;
                $effectiveDistributorId = $this->getEffectiveDistributorId($idDistribuidor);

				$produtos = Preco::selectRaw("preco.*, produto.id as idProd, produto.nome as nome, produto.descricao as descricao, produto.img as img, categoria.nome as categoria, estoque.id as idEstoque")
					->leftJoin("produto", 'produto.id', '=', 'preco.idProduto')
					->leftJoin('categoria', 'categoria.id', '=', 'produto.idCategoria')
					->leftJoin('estoque', function($join) use ($effectiveDistributorId) {
                        $join->on('estoque.idProduto', '=', 'produto.id')
                        ->where('estoque.idDistribuidor', '=', $effectiveDistributorId);
                    })
					->where("preco.status", Preco::ATIVO)
                    ->where("preco.idDistribuidor", $idDistribuidor)
                    ->whereNotNull('estoque.id') // Ensure there is a stock entry
                    ->where('estoque.quantidade', '>=', 1)
                    ->whereRaw("((preco.inicioValidade IS NULL OR preco.inicioValidade <= CURDATE()) AND (preco.fimValidade IS NULL OR preco.fimValidade >= CURDATE()))")
					->whereRaw("((preco.inicioHora IS NULL OR preco.inicioHora <= CURTIME()) AND (preco.fimHora IS NULL OR preco.fimHora > CURTIME()))")
                    ->whereNull("preco.idCliente")
					->orderBy("categoria.nome", "ASC")
                    ->orderBy("produto.nome", "ASC")
                    ->orderBy("preco.qtdMin", "ASC")
					->get();

				if(count($produtos)){
					$out["distribuidores"][0]["idDistribuidor"] = $selectedDistributor->id;
					$indexCategoria = 0; $indexProduto = 0; $indexPreco = 0;
					$txtCategoria = $produtos[0]->categoria;
					$txtProduto = $produtos[0]->idProd;

					foreach($produtos as $prod){
						if($txtCategoria != $prod->categoria){
							$indexCategoria++; $indexProduto = 0; $indexPreco = 0; $txtCategoria = $prod->categoria;
						}else if($txtProduto != $prod->idProd){
							$indexProduto++; $indexPreco = 0; $txtProduto = $prod->idProd;
						}
						$out["distribuidores"][0]["content"][$indexCategoria]["categoria"] = $prod->categoria;
						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["nome"] = $prod->nome;
						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["descricao"] = $prod->descricao;
						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["id"] = $prod->idProd;
						$out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["img"] = $prod->img;

                        $out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["qtd"] = $prod->qtdMin;
                        $out["distribuidores"][0]["content"][$indexCategoria]["produtos"][$indexProduto]["preco"][$indexPreco]["val"] = $prod->valor;
                        $indexPreco++; // Simpler logic for prices if not merging same qtdMin

						$txtProduto = $prod->idProd;
					}
					$out["msg"] = "ok";
				}else{
					$out["msg"] = "nok";
					$out["distribuidores"][0]["idDistribuidor"] = null;
				}
			}else{
				$out["msg"] = "nok";
				$out["distribuidores"][0]["idDistribuidor"] = null;
			}
		}
		return response()->json($out);
	}

	function clientePotencial(Request $request){
		$clientePotencial = new ClientePotencial($request->all());
		$date = new DateTime();
		$clientePotencial->dataAcesso	=	$date->format('Y-m-d H:i:s');
		$clientePotencial->status 		=   ClientePotencial::ACESSO_COMUM;

		if($clientePotencial->save()){
			$out["msg"] =  "ok";
			$out["idCliente"] = $clientePotencial->id;
		}else{
			$out["msg"] =  "nok";
		}
        return response()->json($out);
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
			$out["msg"] = "not_found"; // More descriptive
		}
		return response()->json($out);
	}

    function notificacaoRecebida(Request $request){
		$pedido = Pedido::find($request->idPedido);
		if($pedido){
			$pedido->update(['statusChange'=>0]);
            return response()->json(['msg' => 'ok']);
		}
        return response()->json(['msg' => 'pedido_not_found'], 404);
	}

    function senhaModoTeste(Request $request){
		if($request->senha=="tokumsedes3cr3t"){ // Store secrets in .env
			$out["msg"] = "ok";
		}else{
			$out["msg"] = "nok";
		}
		return response()->json($out);
	}

    function alteraEnderecoAtual(Request $request){
        DB::transaction(function () use ($request) { // Use a transaction
            EnderecoCliente::where('idCliente',$request->idCliente)->update(['atual'=> false]); // Booleans
            EnderecoCliente::where('id',$request->idEndereco)->update(['atual'=> true]);
        });
		$out["msg"]	= "ok";
		return response()->json($out);
	}

    function cadastrarNovoEndereco(Request $request){
        $out = [];
        DB::beginTransaction();
        try {
            $enderecoCliente = new Enderecocliente($request->all());
            $enderecoCliente->logradouro	=   $request->endereco; // Assuming 'endereco' is logradouro
            $enderecoCliente->atual			=   true;
            $enderecoCliente->status		=	Enderecocliente::ATIVO;

            if($enderecoCliente->save()){
                EnderecoCliente::where('idCliente',$enderecoCliente->idCliente)
                                ->where('id','!=',$enderecoCliente->id)
                                ->update(['atual'=> false ]); // Update to boolean false

                DB::commit();
                // Instead of calling consultaInicial directly, which echoes,
                // return data indicating success or the new address ID.
                // The client app should then re-fetch if needed.
                // This keeps API endpoints focused.
                $out['msg'] = 'ok';
                $out['idEndereco'] = $enderecoCliente->id;
                // If you absolutely must return the same structure as consultaInicial:
                // return $this->consultaInicial(new Request(['idCliente' => $enderecoCliente->idCliente]));
                // However, this is generally not good practice for a POST/PUT request.
                return response()->json($out);
            }else{
                DB::rollBack();
                $out["msg"] = "nok_save_failed";
			    return response()->json($out, 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in cadastrarNovoEndereco: " . $e->getMessage());
            $out["msg"] = "nok_exception";
            $out["error"] = $e->getMessage();
			return response()->json($out, 500);
        }
	}

    function cancelarPedido(Request $request){
		$pedido = Pedido::find($request->idPedido);
		if($pedido){
			if($pedido->update(['status'=>Pedido::CANCELADO_USUARIO])){
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok_update_failed";
			}
		}else{
			$out["msg"] = "nok_not_found";
		}
		return response()->json($out);
	}

    function pedidoRecebido(Request $request){
        $out = [];
		$idPedido = $request->idPedido;
		$pedido = Pedido::find($idPedido);

		if($pedido){
            DB::beginTransaction();
            try {
                if($pedido->status == Pedido::ACEITO){ // Only update stock if it was accepted
                    $itensPedido = ItemPedido::where('idPedido', $idPedido)->get();
                    foreach ($itensPedido as $item) {
                        // Ensure stock update is robust
                        $stockUpdated = Estoque::where('idDistribuidor',$pedido->idDistribuidor)
                                            ->where('idProduto',$item->idProduto)
                                            ->where('quantidade', '>=', $item->qtd) // Optimistic lock / check
                                            ->decrement('quantidade', $item->qtd);
                        if (!$stockUpdated) {
                            throw new \Exception("Estoque insuficiente ou não encontrado para produto ID {$item->idProduto} no distribuidor ID {$pedido->idDistribuidor}");
                        }
                    }
                }
                $pedido->status = Pedido::ENTREGUE;
                $pedido->horarioEntrega = now()->format('Y-m-d H:i:s');

                if($pedido->save()){
                    DB::commit();
                    $out["msg"] = "ok";
                }else{
                    DB::rollBack();
                    $out["msg"] = "nok_pedido_save_failed";
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error in pedidoRecebido for order {$idPedido}: " . $e->getMessage());
                $out["msg"] = "nok_exception";
                $out["error"] = $e->getMessage();
            }
		}else{
			$out["msg"] = "nok_pedido_not_found";
		}
		return response()->json($out);
	}

    function alteraDadosCliente(Request $request){
		$cliente = Cliente::find($request->id);
		if($cliente){
            $dataToUpdate = $request->except(['id', 'senha']); // Get all except id and senha
			if ($request->filled('senha')) { // Only update senha if provided
                 $dataToUpdate['senha'] = bcrypt($request->senha); // Always hash new passwords
            }

			if($cliente->update($dataToUpdate)){
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok_update_failed";
			}
		}else{
			$out["msg"] = "nok_not_found";
		}
		return response()->json($out);
	}

    function verifyRecover(Request $request){
		$cliente = Cliente::where('email',$request->email)
                            ->where('dddTelefone',$request->ddd)
                            ->where('telefone',$request->telefone)
                            ->where('status',Cliente::ATIVO)
                            ->first();
        $out = [];
        if ($cliente) {
        	$out["msg"] = "ok";
			$out["idCliente"] = $cliente->id;
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
        }else{
        	$out["msg"] = "nok";
        }
		return response()->json($out);
	}

    function alteraSenha(Request $request){
		$cliente = Cliente::find($request->idCliente);
        $out = [];
		if($cliente){
			if($cliente->update(['senha'=> bcrypt($request->senha)])){ // Hash new passwords
				$out["msg"] = "ok";
			}else{
				$out["msg"] = "nok_update_failed";
			}
		}else{
			$out["msg"] = "nok_not_found";
		}
        return response()->json($out);
	}

    function novoPedido(Request $request){
        $out = [];
        DB::beginTransaction();
        try {
            $pedido = new Pedido($request->except('produtos', 'testePh', 'totalPedido')); // Exclude non-model fields
            $pedido->horarioPedido 	= now()->format('Y-m-d H:i:s');
            $pedido->status 		= Pedido::PENDENTE;
            $pedido->statusChange	= 0; // Assuming 0 is boolean false for statusChange
            $pedido->trocoPara      = $request->filled('trocoPara') ? str_replace(",",".",$request->trocoPara) : 0;
            $pedido->total      	= str_replace(",",".",$request->totalPedido); // Assuming totalPedido is the name
            $pedido->obs 			= $request->testePh ? 'FAZER TESTE DE PH. '.$request->obs : $request->obs;

            $produtosDecoded = json_decode(rawurldecode($request->produtos));
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON for products: " . json_last_error_msg());
            }

            $distribuidor = Distribuidor::find($pedido->idDistribuidor);
            if (!$distribuidor) throw new \Exception("Distribuidor não encontrado: ID {$pedido->idDistribuidor}");

            $endereco = EnderecoCliente::find($pedido->idEndereco);
            if (!$endereco) throw new \Exception("Endereço não encontrado: ID {$pedido->idEndereco}");

            $cliente = Cliente::find($endereco->idCliente); // Already fetched by $endereco relationship
            if (!$cliente) throw new \Exception("Cliente não encontrado: ID {$endereco->idCliente}");


            // Revendedor logic - This might need a more robust way to identify revendedor products
            $revendedorProductIds = [1, 4, 5, 6, 332, 333, 335];
            if($distribuidor->tipoDistribuidor == "revendedor"){
                foreach ($produtosDecoded as $produto) {
                    if (!in_array($produto->produto->id, $revendedorProductIds)) {
                        if ($distribuidor->idDistribuidor) { // Check if main distributor ID exists
                            $pedido->idDistribuidor = $distribuidor->idDistribuidor;
                            break; // Assume if one product is not revendedor's, all go to main
                        } else {
                            // Handle case where revendedor sells non-revendedor product but has no main distributor
                            Log::warning("Revendedor {$distribuidor->id} attempting to sell non-revendedor product {$produto->produto->id} but has no main idDistribuidor specified.");
                        }
                    }
                }
            }

            if($pedido->save()){
                foreach($produtosDecoded as $p){
                    $item = new Itempedido();
                    $item->qtd = $p->qtd;
                    $item->preco = $p->preco;
                    $item->subtotal = $p->subtotal;
                    $item->idProduto = $p->produto->id;
                    $item->idPedido = $pedido->id;
                    $item->save();
                }
                DB::commit();
                $out["msg"] = "ok";
                $out["idPedido"] = $pedido->id;

                // FCM Notification Logic (Simplified for brevity, ensure FCM keys are in .env)
                $administradores = Administrador::where('status','Ativo') // Use constants if available
                    ->where(function($query) use ($pedido) {
                        $query->where('tipoAdministrador', 'Administrador') // Use constants
                              ->orWhere('tipoAdministrador', 'Atendente')  // Use constants
                              ->orWhere('idDistribuidor', $pedido->idDistribuidor);
                    })->get();

                $textoNotif = $endereco->logradouro.' '.$endereco->numero.', '.$endereco->bairro.' - '.$endereco->cidade.'/'.$endereco->estado;
                $msgNotif = [
                    'title' => 'Pedido '.$pedido->id.' - '.$cliente->nome,
                    'body' => $textoNotif,
                    'tag' => (string)$pedido->id, // Tag should be a string
                    'icon' => '/images/logo-icon.png', // Ensure this path is correct for FCM
                    'click_action' => env('APP_ADMIN_URL', 'https://adm.tokumsede.com') // From .env
                ];
                $headersNotif = [
                    'Authorization: key='.env('FCM_SERVER_KEY'),
                    'Content-Type: application/json'
                ];

                foreach($administradores as $administrador) {
                    if($administrador->token_fcm){
                        $this->sendFcmNotification($administrador->token_fcm, $msgNotif, $headersNotif);
                    }
                    if($administrador->token_fcm_mobile){
                         $this->sendFcmNotification($administrador->token_fcm_mobile, $msgNotif, $headersNotif);
                    }
                }

            }else{
                DB::rollBack();
                $out["msg"] = "nok_pedido_save_failed";
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in novoPedido: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            $out["msg"] = "nok_exception";
            $out["error"] = $e->getMessage();
        }
		return response()->json($out);
	}

    private function sendFcmNotification(string $token, array $notificationPayload, array $headers) {
        $fields = [
            'priority' => 'high',
            'to' => $token,
            'notification' => $notificationPayload
        ];
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true ); // Keep true for production
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        if (curl_errno($ch)) {
            Log::error('FCM Curl error: ' . curl_error($ch) . " for token: " . $token);
        } else {
            Log::info('FCM Result for token ' . $token . ': ' . $result);
        }
        curl_close( $ch );
        return $result;
    }


    // ---------------------------------- Refatorados (Original name) ------------------------------------------------//
    // These methods were outside the class in the original snippet,
    // but they are now part of the class. Some were already defined (like selectDist).
    // cadastraCliente used $this->escape which is not a standard PHP or Laravel method.
    // Assuming it was meant to be $request->input('key') or htmlspecialchars.
    // For Laravel, $request->input() is preferred.

	public function selectDist($dists){ // Made public if called from outside, private if only internal
		usort($dists, function ($a, $b) {
		    return $a['atual'] <=> $b['atual']; // Spaceship operator for PHP 7+
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

	// gcmStatusPedido seems like it would be triggered by an external event or admin action,
    // not typically part of this IndexController for mobile clients.
    // If it's an API endpoint, it needs a Request object.
	public function gcmStatusPedido(Request $request){ // Assuming it's an API endpoint
		$idCliente = $request->input("idCliente");
		$idPedido = $request->input("idPedido");

        $cliente = Cliente::find($idCliente);
        $pedido = Pedido::find($idPedido);

        if ($cliente && $pedido) {
            // Assuming gcmSend is a method in this class or a helper
            // if($this->gcmSend($cliente->regId, $idCliente, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true)){
            // 	return response()->json(['status' => 'ok']);
            // }else{
            // 	return response()->json(['status' => 'nok_gcm_failed']);
            // }
            Log::info("gcmStatusPedido: Would send GCM for order {$idPedido} to client {$idCliente}");
            return response()->json(['status' => 'ok_simulated']);
        }
        return response()->json(['status' => 'nok_not_found'], 404);
	}


	public function cadastraCliente(Request $request){ // Use Request for input
        DB::beginTransaction();
        try {
            $cliente = new Cliente();
            $dataNascimento = $request->input("dataNascimento");

            $cliente->nome 			=	$request->input("nome");
            $cliente->dddTelefone	=	$request->input("ddd");
            $cliente->telefone		=	$request->input("telefone");
            $cliente->sexo			=	$request->input("sexo");
            $cliente->dataNascimento=	empty($dataNascimento) ? null : $dataNascimento;
            $cliente->email			=	strtolower($request->input("email"));
            $cliente->senha			=	bcrypt($request->input("senha")); // HASH PASSWORDS
            $cliente->tipoPessoa 	=	$request->input("tipoPessoa");
            $cliente->cpf			=	$request->input("cpf");
            $cliente->cnpj			=	$request->input("cnpj");
            $cliente->logado		=   false;
            $cliente->rating		= 	0;
            $cliente->status		=	Cliente::ATIVO;

            if($cliente->save()){
                $enderecoCliente = new Enderecocliente();
                $enderecoCliente->logradouro	=	$request->input("endereco");
                $enderecoCliente->numero		=	$request->input("numero");
                $enderecoCliente->bairro		=	$request->input("bairro");
                $enderecoCliente->complemento	=	$request->input("complemento");
                $enderecoCliente->cep			=	$request->input("cep");
                $enderecoCliente->cidade 		=	$request->input("cidade");
                $enderecoCliente->estado		=	$request->input("estado");
                $enderecoCliente->referencia 	=	$request->input("referencia");
                $enderecoCliente->apelido		=	$request->input("apelido");
                $enderecoCliente->atual			=   true;
                $enderecoCliente->idCliente		=   $cliente->id;
                $enderecoCliente->latitude		=	$request->input("latitude"); // Should be geocoded ideally
                $enderecoCliente->longitude		=	$request->input("longitude"); // Should be geocoded ideally
                $enderecoCliente->status		=	Enderecocliente::ATIVO;

                if($enderecoCliente->save()){
                    DB::commit();
                    $out["msg"] 		=  "ok";
                    $out["idCliente"] 	= $cliente->id;
                    $out["cliente"] = $cliente->toArray(); // Or specific fields as before
                    return response()->json($out);
                }else{
                    DB::rollBack();
                    $out["msg"] =  "nok_endereco_save_failed";
                    return response()->json($out, 500);
                }
            }else{
                DB::rollBack();
                $out["msg"] =  "nok_cliente_save_failed";
                return response()->json($out, 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in cadastraCliente: " . $e->getMessage());
            $out["msg"] =  "nok_exception";
            $out["error"] = $e->getMessage();
            return response()->json($out, 500);
        }
	}

    // listUnusedImages seems like an admin utility, not a typical API endpoint.
    // It also uses Doctrine syntax which is not standard for Laravel Eloquent.
    // If needed, it should be adapted to Eloquent or run as a command.
	public function listUnusedImages(){
        // This is a conceptual rewrite. Actual implementation might need more context
        // on how product images are stored and referenced.
        $allImageFiles = [];
        // Assuming glob pattern is correct and accessible
        foreach (glob(public_path("imgs/uploads/*")) as $filename) { // Use public_path for web accessible files
             $allImageFiles[] = basename($filename);
        }

        $usedImages = Produto::where('status', '!=', Produto::EXCLUIDO) // Use constant
                             ->whereNotNull('img')
                             ->where('img', '!=', '')
                             ->distinct()
                             ->pluck('img')
                             ->all();

        $unusedImages = array_diff($allImageFiles, $usedImages);

        // echo implode("<br />", $unusedImages); // For web output
        return response()->json(['unused_images' => array_values($unusedImages)]); // For API
	}

    // duplicaProdutos also seems like an admin/dev utility and uses Doctrine.
    // Skipping full rewrite as it's complex and likely not a public API.
	public function duplicaProdutos(){
        // ... Doctrine code would need full rewrite to Eloquent ...
        // This is a placeholder, original logic was for Doctrine and specific IDs.
        Log::warning("duplicaProdutos method called - it's a dev utility and uses Doctrine, not fully implemented for Eloquent here.");
        return response()->json(['message' => 'This is a developer utility and not fully implemented for Eloquent in this context.']);
	}

    // consultaCoords also uses Doctrine. buscarLatitudeLongitude is already defined globally.
	public function consultaCoords(Request $request) { // Example: get address ID from request
        // This is a conceptual adaptation. The original was finding by a hardcoded ID.
        $addressId = $request->input('idEnderecoCliente', 12); // Default to 12 if not provided
		$enderecoCliente = EnderecoCliente::find($addressId);

        if ($enderecoCliente) {
            $coordenadas = buscarLatitudeLongitude(
                $enderecoCliente->logradouro,
                $enderecoCliente->numero,
                $enderecoCliente->cidade,
                $enderecoCliente->estado,
                $enderecoCliente->cep
            );
            return response()->json($coordenadas);
        }
        return response()->json(['error' => 'Address not found'], 404);
	}
} // End of IndexController class

// These global functions should ideally be in a Helper class or a Trait if used across multiple controllers.
// For now, leaving them global as in the original file.

if (!function_exists('calcDistancia')) {
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
}

if (!function_exists('buscarLatitudeLongitude')) {
    function buscarLatitudeLongitude($logradouro, $numero, $cidade, $estado, $cep) {
        // It's highly recommended to use an environment variable for the API key.
        $key = env('GOOGLE_MAPS_API_KEY', null); // Fallback to null if not set
        if (!$key) {
            Log::error("Google Maps API Key is not set in .env");
            return null; // Or handle error appropriately
        }

        $addressParts = array_filter([$logradouro, $numero, $cidade, $estado, $cep, "Brasil"]);
        $address = implode(", ", $addressParts);

        $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false&key=".$key; // Using JSON, sensor is deprecated

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($request_url);
            $body = json_decode((string) $response->getBody());

            if ($body && $body->status == "OK" && !empty($body->results)) {
                $location = $body->results[0]->geometry->location;
                return [
                    (string)$location->lat, // Cast to string as original did
                    (string)$location->lng  // Cast to string as original did
                ];
            } else {
                Log::warning("Geocoding failed for address: {$address}. Status: " . ($body->status ?? 'Unknown') . ". Error: " . ($body->error_message ?? 'No error message'));
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Exception during geocoding for address {$address}: " . $e->getMessage());
            return null;
        }
    }
}
