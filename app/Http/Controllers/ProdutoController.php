<?php

namespace App\Http\Controllers;


use App\Models\Produto;
use App\Models\Composicao;
use App\Models\Preco;
use App\Models\EnderecoCliente;
use App\Models\Distribuidor;
use App\Models\Feriado;
use App\Models\HorarioFuncionamento;
use App\Models\NovoHorarioFuncionamento;
use App\Models\TaxaEntrega;
use App\Models\Estoque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Barryvdh\Debugbar\Facades\Debugbar;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->tipoAdministrador == "Distribuidor") {
            $produtos = Produto::where('status', '!=', Produto::EXCLUIDO)->with('categoria:id,nome', 'estoque:id,idProduto,quantidade')->withCount('preco')->get();
        } else {
            $produtos = Produto::where('status', '!=', Produto::EXCLUIDO)->with('categoria:id,nome')->get();
        }
        return $produtos;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produto = new Produto($request->all());
        $produto->status = Produto::ATIVO;

        if ($produto->save()) {

            $distribuidores = Distribuidor::get();

            foreach ($distribuidores as $distribuidor) {
                $estoque = new Estoque();
                $estoque->idDistribuidor = $distribuidor["id"];
                $estoque->idProduto = $produto->id;
                $estoque->quantidade = 0;
                $estoque->save();
            }

            if ($request->composicao == 1) {
                $request['idComposicao'] = $produto->id;
                $itens = $request->itensComposicao;
                foreach ($itens as $item) {
                    //1-4
                    $it = explode('-', $item);
                    $request['idComponente'] = $it[0];
                    $request['quantidade'] = $it[1];
                    //Faz o cadastro da composição
                    $composicao = new Composicao($request->all());
                    $composicao->save();

                    $this->composicoesArray = array($produto->id);
                    $distribuidores = Distribuidor::get();
                    foreach ($distribuidores as $distribuidor) {
                        $this->atualizaComposicoes($distribuidor->id);
                    }
                }
                if ($composicao->save()) {
                    return $produto->id;
                } else {
                    return response("Erro ao cadastrar o produto. Tente novamente ou contate o administrador do sistema.", 200);
                }
            }
            return $produto->id;
        } else {
            return response("Erro ao cadastrar o produto. Tente novamente ou contate o produto.", 200);
        }

    }

    private function calculateDistance($lat1, $long1, $lat2, $long2)
    {
        $d2r = 0.017453292519943295769236;
        $dlong = ($long2 - $long1) * $d2r;
        $dlat = ($lat2 - $lat1) * $d2r;

        $temp_sin = sin($dlat / 2.0);
        $temp_cos = cos($lat1 * $d2r);
        $temp_sin2 = sin($dlong / 2.0);

        $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return 6368.1 * $c;
    }
    private function getFormattedWorkingHours($novoHorarioFuncionamento)
    {
        return [
            0 => [
                'funciona' => $novoHorarioFuncionamento->domingo,
                'inicio' => $novoHorarioFuncionamento->inicioDomingo,
                'fim' => $novoHorarioFuncionamento->fimDomingo
            ],
            1 => [
                'funciona' => $novoHorarioFuncionamento->segunda,
                'inicio' => $novoHorarioFuncionamento->inicioSegunda,
                'fim' => $novoHorarioFuncionamento->fimSegunda
            ],
            2 => [
                'funciona' => $novoHorarioFuncionamento->terca,
                'inicio' => $novoHorarioFuncionamento->inicioTerca,
                'fim' => $novoHorarioFuncionamento->fimTerca
            ],
            3 => [
                'funciona' => $novoHorarioFuncionamento->quarta,
                'inicio' => $novoHorarioFuncionamento->inicioQuarta,
                'fim' => $novoHorarioFuncionamento->fimQuarta
            ],
            4 => [
                'funciona' => $novoHorarioFuncionamento->quinta,
                'inicio' => $novoHorarioFuncionamento->inicioQuinta,
                'fim' => $novoHorarioFuncionamento->fimQuinta
            ],
            5 => [
                'funciona' => $novoHorarioFuncionamento->sexta,
                'inicio' => $novoHorarioFuncionamento->inicioSexta,
                'fim' => $novoHorarioFuncionamento->fimSexta
            ],
            6 => [
                'funciona' => $novoHorarioFuncionamento->sabado,
                'inicio' => $novoHorarioFuncionamento->inicioSabado,
                'fim' => $novoHorarioFuncionamento->fimSabado
            ],
            // ... repeat for other days
            'pausaAlmoco' => $novoHorarioFuncionamento->pausaAlmoco,
            'inicioAlmoco' => $novoHorarioFuncionamento->inicioAlmoco,
            'fimAlmoco' => $novoHorarioFuncionamento->fimAlmoco
        ];
    }
    private function getActiveProducts($distribuidorId)
    {
        return DB::table('preco')
            ->leftJoin('produto', 'produto.id', '=', 'preco.idProduto')
            ->leftJoin('estoque', 'estoque.id', '=', 'preco.idEstoque')
            ->select([
                'preco.*',
                'produto.id as idProd',
                'produto.nome as nome',
                'produto.img as img'
            ])
            ->where([
                ['preco.status', '=', 1],
                ['preco.idDistribuidor', '=', $distribuidorId],
                ['estoque.quantidade', '>', 0]
            ])
            ->where(function ($query) {
                $query->whereNull('preco.inicioValidade')
                    ->orWhere('preco.inicioValidade', '<=', DB::raw('curdate()'));
            })
            ->where(function ($query) {
                $query->whereNull('preco.fimValidade')
                    ->orWhere('preco.fimValidade', '>', DB::raw('curdate()'));
            })
            ->orderBy('produto.id')
            ->orderBy('preco.qtdMin')
            ->get();
    }
    private function getNearbyDistributors($enderecoCliente)
    {
        $fator = doubleval(0.2);
        return DB::table('distribuidor')
            ->join('enderecoDistribuidor', 'enderecoDistribuidor.id', '=', 'distribuidor.idEnderecoDistribuidor')
            ->select(
                'distribuidor.*',
                'enderecoDistribuidor.latitude as latitude',
                'enderecoDistribuidor.longitude as longitude',
                'enderecoDistribuidor.distanciaMaxima as distanciaMaxima'
            )
            ->whereRaw("status = " . Distribuidor::ATIVO . " AND enderecoDistribuidor.latitude + $fator >= " . $enderecoCliente->latitude
                . " AND enderecoDistribuidor.latitude - $fator <= " . $enderecoCliente->latitude . " AND enderecoDistribuidor.longitude + $fator >= "
                . $enderecoCliente->longitude . " AND enderecoDistribuidor.longitude - $fator <= " . $enderecoCliente->longitude)
            ->get();
    }

    private function selectNearestDistributor($distribuidores, $enderecoCliente)
    {
        $indexDistribuidor = 0;
        $dists = array();
        foreach ($distribuidores as $pos2 => $d) {
            if ($enderecoCliente->latitude == NULL || $enderecoCliente->longitude == NULL) {
                $dist = PHP_INT_MAX;
            } else {
                $lat1 = $d->latitude;
                $long1 = $d->longitude;
                $lat2 = $enderecoCliente->latitude;
                $long2 = $enderecoCliente->longitude;

                $dist = $this->calculateDistance($lat1, $long1, $lat2, $long2);
            }
            $dists[$pos2] = array("max" => $d->distanciaMaxima, "atual" => $dist, "pos" => $pos2);//array("max" => $d["distanciaMaxima"], "atual" => $dist, "pos" => $pos2);
        }
        $indexDistribuidor = $this->selectDist($dists);
        return $distribuidores[$indexDistribuidor];
    }

    private function formatProductsOutput($produtos, $idCliente)
    {
        // Early return if no products
        if (empty($produtos[0]->idProd)) {
            return [];
        }

        $out = [];
        $currentProduct = null;
        $indexProduto = -1;

        foreach ($produtos as $prod) {
            // Create new product entry if different from current
            if ($currentProduct !== $prod->idProd) {
                $indexProduto++;
                $currentProduct = $prod->idProd;

                // Initialize new product structure
                $out[$indexProduto] = [
                    "nome" => $prod->nome,
                    "id" => $prod->idProd,
                    "img" => $prod->img,
                    "preco" => [],
                    "precoEspecial" => [],
                    // "categoria" => $prod->categoria
                ];
            }
            // Add pricing based on client
            if ($idCliente == $prod->idCliente) {
                $out[$indexProduto]['precoEspecial'][] = [
                    'qtd' => $prod->qtdMin,
                    'val' => $prod->valor
                ];
            } else if ($prod->idCliente === null) {
                $out[$indexProduto]['preco'][] = [
                    'qtd' => $prod->qtdMin,
                    'val' => $prod->valor
                ];
            }
            debugbar::info($out[$indexProduto]);
            if (empty($out[$indexProduto]['precoEspecial'])) {
                unset($out[$indexProduto]['precoEspecial']);
            }
            // if($indexPrecoEspecial>0){
            //     $out[$indexProduto]['preco'] = $out[$indexProduto]['precoEspecial'];
            // }
        }
        Debugbar::info($out);
        return $out;
    }

    private function getUpcomingHolidays($distribuidorId)
    {
        return Feriado::where('idDistribuidor', $distribuidorId)->whereRaw('dataFeriado >= curdate()')->selectRaw("feriado.id as id, feriado.dataFeriado as dataFeriado")->get();
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function show($idEnderecoCliente)
    {
        debugbar::info($idEnderecoCliente);
        $enderecoCliente = EnderecoCliente::findOrFail($idEnderecoCliente);
        $distribuidores = $this->getNearbyDistributors($enderecoCliente);

        if (empty($distribuidores)) {
            return $enderecoCliente->idCliente;
        }

        $distribuidor = $this->selectNearestDistributor($distribuidores, $enderecoCliente);
        if (!$distribuidor) {
            return $enderecoCliente->idCliente;
        }

        $horarioFuncionamento = HorarioFuncionamento::find($distribuidor->idHorarioFuncionamento);
        $novoHorarioFuncionamento = NovoHorarioFuncionamento::find($distribuidor->idNovoHorarioFuncionamento);
        $horario = $this->getFormattedWorkingHours($novoHorarioFuncionamento);

        $taxaEntrega = TaxaEntrega::find($distribuidor->idTaxaEntrega);
        $feriados = $this->getUpcomingHolidays($distribuidor->id);

        $produtos = $this->getActiveProducts($distribuidor->id);
        $formattedProducts = $this->formatProductsOutput($produtos, $enderecoCliente->idCliente);

        return [
            $formattedProducts,
            $distribuidor,
            $enderecoCliente,
            $horarioFuncionamento,
            $taxaEntrega,
            $feriados,
            $horario
        ];
    }


    // public function show($idEnderecoCliente)
    // {
    //     $enderecoCliente = EnderecoCliente::find($idEnderecoCliente);
    //     $fator = doubleval(0.2);

    //     $distribuidores = DB::table('distribuidor')
    //         ->join('enderecoDistribuidor', 'enderecoDistribuidor.id', '=', 'distribuidor.idEnderecoDistribuidor')
    //         ->select('distribuidor.*',
    //             'enderecoDistribuidor.latitude as latitude',
    //             'enderecoDistribuidor.longitude as longitude',
    //             'enderecoDistribuidor.distanciaMaxima as distanciaMaxima')
    //         ->whereRaw("status = ".Distribuidor::ATIVO." AND enderecoDistribuidor.latitude + $fator >= ".$enderecoCliente->latitude
    // 				." AND enderecoDistribuidor.latitude - $fator <= ".$enderecoCliente->latitude." AND enderecoDistribuidor.longitude + $fator >= "
    // 				.$enderecoCliente->longitude." AND enderecoDistribuidor.longitude - $fator <= ".$enderecoCliente->longitude)
    //         ->get();

    //     $indexDistribuidor = 0;
    //     $dists = array();
    //     foreach($distribuidores as $pos2 => $d){
    // 		if($enderecoCliente->latitude == NULL || $enderecoCliente->longitude == NULL){
    // 			$dist = PHP_INT_MAX;
    // 		}else{
    // 			//calcDistancia($d->latitude, $d->longitude, $enderecoCliente->latitude, $enderecoCliente->longitude);
    //             $d2r = 0.017453292519943295769236;

    //             $dlong = ($enderecoCliente->longitude - $d->longitude) * $d2r;
    //             $dlat = ($enderecoCliente->latitude - $d->latitude) * $d2r;

    //             $temp_sin = sin($dlat/2.0);
    //             $temp_cos = cos($d->latitude * $d2r);
    //             $temp_sin2 = sin($dlong/2.0);

    //             $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
    //             $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

    //             $dist = 6368.1 * $c;
    //         }
    // 		$dists[$pos2] = array("max" => $d->distanciaMaxima, "atual" => $dist, "pos" => $pos2);//array("max" => $d["distanciaMaxima"], "atual" => $dist, "pos" => $pos2);
    //     }
    //     $indexDistribuidor = $this->selectDist($dists);
    //     if($indexDistribuidor!=-1){
    //         $distribuidor = $distribuidores[$indexDistribuidor];

    //         $horarioFuncionamento = HorarioFuncionamento::find($distribuidor->idHorarioFuncionamento);
    //         $novoHorarioFuncionamento = NovoHorarioFuncionamento::find($distribuidor->idNovoHorarioFuncionamento);
    //         $horario[0]["funciona"] = $novoHorarioFuncionamento->domingo;
    //         $horario[0]["inicio"] 	= $novoHorarioFuncionamento->inicioDomingo;
    //         $horario[0]["fim"] 		= $novoHorarioFuncionamento->fimDomingo;
    //         $horario[1]["funciona"] = $novoHorarioFuncionamento->segunda;
    //         $horario[1]["inicio"] 	= $novoHorarioFuncionamento->inicioSegunda;
    //         $horario[1]["fim"] 		= $novoHorarioFuncionamento->fimSegunda;
    //         $horario[2]["funciona"] = $novoHorarioFuncionamento->terca;
    //         $horario[2]["inicio"] 	= $novoHorarioFuncionamento->inicioTerca;
    //         $horario[2]["fim"] 		= $novoHorarioFuncionamento->fimTerca;
    //         $horario[3]["funciona"] = $novoHorarioFuncionamento->quarta;
    //         $horario[3]["inicio"] 	= $novoHorarioFuncionamento->inicioQuarta;
    //         $horario[3]["fim"] 		= $novoHorarioFuncionamento->fimQuarta;
    //         $horario[4]["funciona"] = $novoHorarioFuncionamento->quinta;
    //         $horario[4]["inicio"] 	= $novoHorarioFuncionamento->inicioQuinta;
    //         $horario[4]["fim"] 		= $novoHorarioFuncionamento->fimQuinta;
    //         $horario[5]["funciona"] = $novoHorarioFuncionamento->sexta;
    //         $horario[5]["inicio"] 	= $novoHorarioFuncionamento->inicioSexta;
    //         $horario[5]["fim"] 		= $novoHorarioFuncionamento->fimSexta;
    //         $horario[6]["funciona"] = $novoHorarioFuncionamento->sabado;
    //         $horario[6]["inicio"] 	= $novoHorarioFuncionamento->inicioSabado;
    //         $horario[6]["fim"] 		= $novoHorarioFuncionamento->fimSabado;
    //         $horario["pausaAlmoco"] = $novoHorarioFuncionamento->pausaAlmoco;
    //         $horario["inicioAlmoco"]= $novoHorarioFuncionamento->inicioAlmoco;
    //         $horario["fimAlmoco"] 	= $novoHorarioFuncionamento->fimAlmoco;
    //         $taxaEntrega = TaxaEntrega::find($distribuidor->idTaxaEntrega);
    //         $feriados = Feriado::where('idDistribuidor',$distribuidor->id)->whereRaw('dataFeriado >= curdate()')->selectRaw("feriado.id as id, feriado.dataFeriado as dataFeriado")->get();

    //         $produtos = DB::table('preco')
    //                 ->leftJoin('produto', 'produto.id', '=', 'preco.idProduto')
    //                 ->leftJoin('estoque', 'estoque.id', '=', 'preco.idEstoque')
    //                 //->leftJoin('produto.categoria', 'catedoria.id', '=', 'produto.idProduto')
    //                 ->select('preco.*', 'produto.id as idProd', 'produto.nome as nome', 'produto.img as img')//, 'categoria.nome as categoria')
    //                 ->where([
    //                     ['preco.status', '=',1],
    //                     ['preco.idDistribuidor', '=', $distribuidor->id],
    //                     ['estoque.quantidade', '>', 0]
    //                 ])
    //                 ->where(function ($query) {
    //                     $query->where('preco.inicioValidade', '=', NULL)
    //                         ->orWhere('preco.inicioValidade', '<=', DB::raw('curdate()'));
    //                 })
    //                 ->where(function ($query) {
    //                     $query->where('preco.fimValidade', '=', NULL)
    //                         ->orWhere('preco.fimValidade', '>', DB::raw('curdate()'));
    //                 })
    //                 ->orderBy('produto.id', 'ASC')
    //                 ->orderBy('preco.qtdMin', 'ASC')
    //                 ->get();

    //         if(!empty($produtos[0]->idProd)){//Verifica se foi encontrado produtos
    //             $indexProduto = 0;
    //             $indexPreco = 0;$indexPrecoEspecial = 0;
    //             $txtProduto = $produtos[0]->idProd;

    //             foreach ($produtos as $prod) {

    //                 if($prod->status == 1) Debugbar::info($prod);

    //                 if ($txtProduto != $prod->idProd) {
    //                     $indexProduto++;
    //                     $indexPreco = 0;
    //                     $indexPrecoEspecial = 0;
    //                     $txtProduto = $prod->idProd;
    //                 }

    //                 $out[$indexProduto]["nome"]      = $prod->nome;
    //                 $out[$indexProduto]["id"] 		 = $prod->idProd;
    //                 $out[$indexProduto]["img"] 		 = $prod->img;
    //                 //$out[$indexProduto]["categoria"] = $prod->categoria;

    //                 if($enderecoCliente->idCliente == $prod->idCliente){
    //                     $out[$indexProduto]['precoEspecial'][$indexPrecoEspecial]['qtd'] = $prod->qtdMin;
    //                     $out[$indexProduto]['precoEspecial'][$indexPrecoEspecial]['val'] = $prod->valor;
    //                     $indexPrecoEspecial++;
    //                 }else if($prod->idCliente== null){
    //                     $out[$indexProduto]['preco'][$indexPreco]['qtd'] = $prod->qtdMin;
    //                     $out[$indexProduto]['preco'][$indexPreco]['val'] = $prod->valor;
    //                     $indexPreco++;
    //                 }
    //                 if($indexPrecoEspecial>0){
    //                     $out[$indexProduto]['preco'] = $out[$indexProduto]['precoEspecial'];
    //                 }
    //             }
    //         }else{
    //             $out = '';
    //         }
    //         return [$out, $distribuidor, $enderecoCliente, $horarioFuncionamento, $taxaEntrega, $feriados, $horario];
    //     }
    //     return $enderecoCliente->idCliente;
    // }

    function calcDistancia($lat1, $long1, $lat2, $long2)
    {

        $d2r = 0.017453292519943295769236;

        $dlong = ($long2 - $long1) * $d2r;
        $dlat = ($lat2 - $lat1) * $d2r;

        $temp_sin = sin($dlat / 2.0);
        $temp_cos = cos($lat1 * $d2r);
        $temp_sin2 = sin($dlong / 2.0);

        $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return 6368.1 * $c;
    }
    function selectDist($dists)
    {
        usort($dists, function ($a, $b) {
            return $a['atual'] > $b['atual'];
        });
        $pos = -1;
        foreach ($dists as $d) {
            if ($d["atual"] <= $d["max"]) {
                $pos = $d["pos"];
                break;
            }
        }
        return $pos;
    }
    function listarProdutos($idDistribuidor, $idCliente)
    {
        if ($idDistribuidor) {
            $distribuidor = Distribuidor::find($idDistribuidor);

            $horarioFuncionamento = HorarioFuncionamento::find($distribuidor->idHorarioFuncionamento);
            $novoHorarioFuncionamento = NovoHorarioFuncionamento::find($distribuidor->idNovoHorarioFuncionamento);
            $horario[0]["funciona"] = $novoHorarioFuncionamento->domingo;
            $horario[0]["inicio"] = $novoHorarioFuncionamento->inicioDomingo;
            $horario[0]["fim"] = $novoHorarioFuncionamento->fimDomingo;
            $horario[1]["funciona"] = $novoHorarioFuncionamento->segunda;
            $horario[1]["inicio"] = $novoHorarioFuncionamento->inicioSegunda;
            $horario[1]["fim"] = $novoHorarioFuncionamento->fimSegunda;
            $horario[2]["funciona"] = $novoHorarioFuncionamento->terca;
            $horario[2]["inicio"] = $novoHorarioFuncionamento->inicioTerca;
            $horario[2]["fim"] = $novoHorarioFuncionamento->fimTerca;
            $horario[3]["funciona"] = $novoHorarioFuncionamento->quarta;
            $horario[3]["inicio"] = $novoHorarioFuncionamento->inicioQuarta;
            $horario[3]["fim"] = $novoHorarioFuncionamento->fimQuarta;
            $horario[4]["funciona"] = $novoHorarioFuncionamento->quinta;
            $horario[4]["inicio"] = $novoHorarioFuncionamento->inicioQuinta;
            $horario[4]["fim"] = $novoHorarioFuncionamento->fimQuinta;
            $horario[5]["funciona"] = $novoHorarioFuncionamento->sexta;
            $horario[5]["inicio"] = $novoHorarioFuncionamento->inicioSexta;
            $horario[5]["fim"] = $novoHorarioFuncionamento->fimSexta;
            $horario[6]["funciona"] = $novoHorarioFuncionamento->sabado;
            $horario[6]["inicio"] = $novoHorarioFuncionamento->inicioSabado;
            $horario[6]["fim"] = $novoHorarioFuncionamento->fimSabado;
            $horario["pausaAlmoco"] = $novoHorarioFuncionamento->pausaAlmoco;
            $horario["inicioAlmoco"] = $novoHorarioFuncionamento->inicioAlmoco;
            $horario["fimAlmoco"] = $novoHorarioFuncionamento->fimAlmoco;
            $taxaEntrega = TaxaEntrega::find($distribuidor->idTaxaEntrega);
            $feriados = Feriado::where('idDistribuidor', $distribuidor->id)->whereRaw('dataFeriado >= curdate()')->selectRaw("feriado.id as id, feriado.dataFeriado as dataFeriado")->get();

            $produtos = DB::table('preco')
                ->leftJoin('produto', 'produto.id', '=', 'preco.idProduto')
                ->leftJoin('estoque', 'estoque.id', '=', 'preco.idEstoque')
                //->leftJoin('produto.categoria', 'catedoria.id', '=', 'produto.idProduto')
                ->select('preco.*', 'produto.id as idProd', 'produto.nome as nome', 'produto.img as img')//, 'categoria.nome as categoria')
                ->where([
                    ['preco.status', '=', 1],
                    ['preco.idDistribuidor', '=', $distribuidor->id],
                    ['estoque.quantidade', '>', 0]
                ])
                ->where(function ($query) {
                    $query->where('preco.inicioValidade', '=', NULL)
                        ->orWhere('preco.inicioValidade', '<=', DB::raw('curdate()'));
                })
                ->where(function ($query) {
                    $query->where('preco.fimValidade', '=', NULL)
                        ->orWhere('preco.fimValidade', '>', DB::raw('curdate()'));
                })
                ->orderBy('produto.id', 'ASC')
                ->orderBy('preco.qtdMin', 'ASC')
                ->get();

            if (!empty($produtos[0]->idProd)) {//Verifica se foi encontrado produtos
                $indexProduto = 0;
                $indexPreco = 0;
                $indexPrecoEspecial = 0;
                $txtProduto = $produtos[0]->idProd;

                foreach ($produtos as $prod) {

                    if ($txtProduto != $prod->idProd) {
                        $indexProduto++;
                        $indexPreco = 0;
                        $indexPrecoEspecial = 0;
                        $txtProduto = $prod->idProd;
                    }

                    $out[$indexProduto]["nome"] = $prod->nome;
                    $out[$indexProduto]["id"] = $prod->idProd;
                    $out[$indexProduto]["img"] = $prod->img;
                    //$out[$indexProduto]["categoria"] = $prod->categoria;

                    if ($idCliente == $prod->idCliente) {
                        $out[$indexProduto]['precoEspecial'][$indexPrecoEspecial]['qtd'] = $prod->qtdMin;
                        $out[$indexProduto]['precoEspecial'][$indexPrecoEspecial]['val'] = $prod->valor;
                        $indexPrecoEspecial++;
                    } else if ($prod->idCliente == null) {
                        $out[$indexProduto]['preco'][$indexPreco]['qtd'] = $prod->qtdMin;
                        $out[$indexProduto]['preco'][$indexPreco]['val'] = $prod->valor;
                        $indexPreco++;
                    }
                    if ($indexPrecoEspecial > 0) {
                        $out[$indexProduto]['preco'] = $out[$indexProduto]['precoEspecial'];
                    }
                }
            } else {
                $out = '';
            }
            return [$out, $distribuidor, $horario, $taxaEntrega, $feriados];
        }
        return 'Erro ao carregar os produtos do distribuidor';
    }
}
