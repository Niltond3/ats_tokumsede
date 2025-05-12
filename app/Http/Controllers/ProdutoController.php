<?php

namespace App\Http\Controllers;


use App\Actions\Products\UpdateProductAction;
use App\Http\Requests\UpdateProductRequest;
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
use App\Traits\UsesDistributorService;

class ProdutoController extends Controller
{
    use UsesDistributorService;

    public function __construct()
    {
        $this->initializeDistributorService();
    }
    private function getEffectiveDistributorId($distributorId)
    {
        $distributor = Distribuidor::find($distributorId);
        return $distributor->getMainDistributorIdAttribute() ?? $distributorId;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->tipoAdministrador == "Distribuidor") {
            $effectiveDistributorId = $this->getEffectiveDistributorId(auth()->user()->idDistribuidor);

            $produtos = Produto::where('status', '!=', Produto::EXCLUIDO)
                ->with(['categoria:id,nome',
                       'composicoes' => function($query) {
                           $query->with('componente:id,nome');
                       },
                       'estoque' => function($query) use ($effectiveDistributorId) {
                           $query->where('idDistribuidor', $effectiveDistributorId);
                       }
                ])
                ->withCount(['preco' => function($query) use ($effectiveDistributorId) {
                    $query->where('idDistribuidor', $effectiveDistributorId);
                }])
                ->get()
                ->map(function($produto) {
                    $composicaoItens = [];
                    if ($produto->composicoes->count() > 0) {
                        $composicaoItens = $produto->composicoes->map(function($comp) {
                            return [
                                'id' => $comp->idComponente,
                                'quantidade' => $comp->quantidade,
                                'nome' => $comp->produto->nome
                            ];
                        });
                    }

                    return [
                        'id' => $produto->id,
                        'idCategoria' => $produto->idCategoria,
                        'nome' => $produto->nome,
                        'descricao' => $produto->descricao,
                        'img' => $produto->img,
                        'status' => $produto->status,
                        'composicao' => $produto->composicao,
                        'componente' => $produto->componente,
                        'categoria' => $produto->categoria,
                        'preco' => $produto->preco,
                        'estoque' => $produto->estoque,
                        'composicaoItens' => $composicaoItens
                    ];
                });
        } else {
            $produtos = Produto::where('status', '!=', Produto::EXCLUIDO)
                ->with(['categoria:id,nome',
                       'composicoes' => function($query) {
                           $query->with('componente:id,nome');
                       }
                ])
                ->get()
                ->map(function($produto) {
                    $composicaoItens = [];
                    if ($produto->composicoes->count() > 0) {
                        $composicaoItens = $produto->composicoes->map(function($comp) {
                            return [
                                'id' => $comp->idComponente,
                                'quantidade' => $comp->quantidade,
                                'nome' => $comp->componente->nome
                            ];
                        });
                    }

                    return [
                        'id' => $produto->id,
                        'idCategoria' => $produto->idCategoria,
                        'nome' => $produto->nome,
                        'descricao' => $produto->descricao,
                        'img' => $produto->img,
                        'status' => $produto->status,
                        'composicao' => $produto->composicao,
                        'componente' => $produto->componente,
                        'categoria' => $produto->categoria,
                        'composicaoItens' => $composicaoItens
                    ];
                });
        }
        return response()->json($produtos);
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
    public function update(UpdateProductRequest $request, Produto $produto)
    {
        try {
            $updateProduct = app(UpdateProductAction::class);
            $updatedProduct = $updateProduct->execute($request, $produto);

            return response()->json([
                'message' => 'Produto atualizado com sucesso',
                'data' => $updatedProduct
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar produto',
                'error' => $e->getMessage()
            ], 500);
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
    Debugbar::info("Distribuidor ID: " . $distribuidorId);
    // Get effective distributor ID (main distributor if in union)
    $effectiveDistributorId = $this->getEffectiveDistributorId($distribuidorId);

    return DB::table('preco')
        ->leftJoin('produto', 'produto.id', '=', 'preco.idProduto')
        ->leftJoin('estoque', function($join) use ($effectiveDistributorId) {
            $join->on('estoque.idProduto', '=', 'produto.id')
                 ->where('estoque.idDistribuidor', '=', $effectiveDistributorId);
        })
        ->select([
            'preco.*',
            'preco.id as idPreco',
            'produto.id as idProd',
            'produto.nome as nome',
            'produto.descricao as descricao',
            'produto.img as img',
            'categoria.nome as categoria',
            'estoque.id as idEstoque'
        ])
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
        $dists = [];
        foreach ($distribuidores as $pos2 => $d) {
            if ($enderecoCliente->latitude === null || $enderecoCliente->longitude === null) {
                $dist = PHP_INT_MAX;
            } else {
                $dist = $this->calculateDistance($d->latitude, $d->longitude, $enderecoCliente->latitude, $enderecoCliente->longitude);
            }
            $dists[$pos2] = [
                "max" => $d->distanciaMaxima,
                "atual" => $dist,
                "pos" => $pos2
            ];
        }
        $indexDistribuidor = $this->selectDist($dists);
        if ($indexDistribuidor === -1) {
            // No distributor was found within range, so return null or handle accordingly.
            return null;
        }
        return $distribuidores[$indexDistribuidor];
    }

    private function sortPriceArrayByPrecoId(array $prices): array
    {
        usort($prices, function ($a, $b) {
            return $a['precoId'] - $b['precoId'];
        });
        return $prices;
    }
    private function formatProductsOutput($produtos, $idCliente = null)
    {
        // Early return if no products
        if (empty($produtos[0]->idProd)) {
            return [];
        }

        $out = [];
        $currentProduct = null;
        $indexProduto = -1;

        foreach ($produtos as $prod) {
            Debugbar::info($prod);
            // Create new product entry if different from current
            if ($currentProduct !== $prod->idProd) {
                $indexProduto++;
                $currentProduct = $prod->idProd;
                            // Get composition data
            $composicao = Composicao::where('idComposicao', $prod->idProd)->with('componente')->get();
            $componentes = [];

            if ($composicao->count() > 0) {
                foreach ($composicao as $comp) {
                    $componentes[] = [
                        'id' => $comp->idComponente,
                        'quantidade' => $comp->quantidade,
                        'nome' => Produto::find($comp->idComponente)->nome
                    ];
                }
            }
                Debugbar::info($prod);
                // Initialize new product structure
                $out[$indexProduto] = [
                    "nome" => $prod->nome,
                    "id" => $prod->idProd,
                    "img" => $prod->img,
                    "descricao" => $prod->descricao,
                    "preco" => [],
                    "precoEspecial" => [],
                    "composicao" => $componentes
                    // "categoria" => $prod->categoria
                ];
            }
            // Add pricing based on client
            if ($idCliente !== null && $idCliente == $prod->idCliente) {
                $out[$indexProduto]['precoEspecial'][] = [
                    'precoId' => $prod->id,
                    'qtd' => $prod->qtdMin,
                    'val' => $prod->valor
                ];
            } else if ($prod->idCliente === null) {
                $out[$indexProduto]['preco'][] = [
                    'precoId' => $prod->id,
                    'qtd' => $prod->qtdMin,
                    'val' => $prod->valor
                ];
            }

            $out[$indexProduto]['preco'] = $this->sortPriceArrayByPrecoId($out[$indexProduto]['preco']);

            if (!empty($out[$indexProduto]['precoEspecial'])) {
                $out[$indexProduto]['precoEspecial'] = $this->sortPriceArrayByPrecoId($out[$indexProduto]['precoEspecial']);
            } else {
                unset($out[$indexProduto]['precoEspecial']);
            }
        }

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
        $enderecoCliente = EnderecoCliente::findOrFail($idEnderecoCliente);
        $distribuidores = $this->getNearbyDistributors($enderecoCliente);


        if (empty($distribuidores)) {
            return $enderecoCliente->idCliente;
        }

        $distribuidor = $this->selectNearestDistributor($distribuidores, $enderecoCliente);
        if (!$distribuidor) {
             // If no distributor is available within the valid distance, handle it accordingly.
        return $enderecoCliente->idCliente;
        }

        $horarioFuncionamento = HorarioFuncionamento::find($distribuidor->idHorarioFuncionamento);
        $novoHorarioFuncionamento = NovoHorarioFuncionamento::find($distribuidor->idNovoHorarioFuncionamento);
        $horario = $this->getFormattedWorkingHours($novoHorarioFuncionamento);

        $taxaEntrega = TaxaEntrega::find($distribuidor->idTaxaEntrega);
        $feriados = $this->getUpcomingHolidays($distribuidor->id);

        $produtos = $this->getActiveProducts($distribuidor->id);
        $idCliente = $enderecoCliente->idCliente;

        $formattedProducts = $this->formatProductsOutput($produtos, $idCliente);

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

    public function showByDistribuidor($distribuidorId, $idCliente = null)
    {
        $produtos = $this->getAllActiveProducts($distribuidorId);
        Debugbar::info($produtos);
        return $this->formatProductsOutput($produtos, $idCliente);
    }
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
                ->select('preco.*', 'produto.id as idProd', 'produto.nome as nome', 'produto.img as img', 'produto.descricao as descricao')//, 'categoria.nome as categoria')
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

                $out = $this->formatProductsOutput($produtos,$idCliente);

            } else {
                $out = '';
            }
            return [$out, $distribuidor, $horario, $taxaEntrega, $feriados];
        }
        return 'Erro ao carregar os produtos do distribuidor';
    }
    /**
 * Update product status
 *
 * @param int $idProduto Product ID
 * @param bool $idStatus New status value
 * @return \Illuminate\Http\Response
 */
public function updateStatus($idProduto, $idStatus)
{
    try {
        $produto = Produto::findOrFail($idProduto);

        // Validate status value
        if (!in_array($idStatus, [1, 3])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value'
            ], 422);
        }

        $produto->status = $idStatus;

        if ($produto->save()) {
            // Update related stocks if product becomes inactive
            if ($idStatus == Produto::INATIVO) {
                Estoque::where('idProduto', $idProduto)
                    ->update(['quantidade' => 0]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully',
                'data' => $produto
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update product status'
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating product status: ' . $e->getMessage()
        ], 500);
    }
}

}
