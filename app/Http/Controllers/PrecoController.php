<?php

namespace App\Http\Controllers;

use App\Models\Preco;
use App\Models\Estoque;
use \Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;

class PrecoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'idProduto' => 'required|exists:produto,id',
            'idDistribuidor' => 'required|exists:distribuidor,id',
            'idCliente' => 'nullable|exists:cliente,id',
            'valor' => 'required|numeric|min:0',
            'qtdMin' => 'required|integer|min:0'
        ]);

        // Find associated stock record
        $estoque = Estoque::where([
            ['idDistribuidor', $validated['idDistribuidor']],
            ['idProduto', $validated['idProduto']]
        ])->firstOrFail();

        // Prepare price data
        $precoData = [
            'status' => Preco::ATIVO,
            'idProduto' => $validated['idProduto'],
            'idDistribuidor' => $validated['idDistribuidor'],
            'idCliente' => $validated['idCliente'] ?? null,
            'valor' => $validated['valor'],
            'qtdMin' => $validated['qtdMin'],
            'idEstoque' => $estoque->id
        ];

        try {
            $preco = Preco::create($precoData);

            return response()->json([
                'status' => 'success',
                'message' => 'Preço cadastrado com sucesso!',
                'data' => $preco
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao cadastrar preço!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\preco  $preco
     * @return \Illuminate\Http\Response
     */
    public function show($idProduto)
    {
        $distribuidor = auth()->user()->idDistribuidor;
        $precos = Preco::where('status', '!=', Preco::EXCLUIDO)->where('idDistribuidor', $distribuidor)->where('idProduto', $idProduto)->with('distribuidor', 'estoque', 'produto')
            ->selectRaw("preco.*, CONCAT('R$', REPLACE(REPLACE(REPLACE(FORMAT( preco.valor , 2),'.',';'),',','.'),';',',')) AS valor, "
                . "date_format(preco.inicioValidade, '%d/%m/%Y') as inicioValidade, date_format(preco.inicioHora, '%H:%i') as inicioHora, "
                . "date_format(preco.fimValidade, '%d/%m/%Y') as fimValidade, date_format(preco.fimHora, '%H:%i') as fimHora")

            ->get();//->with('distribuidor','estoque','produto')
        return $precos;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\preco  $preco
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
{
    Debugbar::info('update');
    Debugbar::info($request);

    $validated = $request->validate([
        'idProduto' => 'required|exists:produto,id',
        'idDistribuidor' => 'required|exists:distribuidor,id',
        'valor' => 'required|numeric|min:0',
        'qtdMin' => 'required|integer|min:0',
        'id' => 'nullable|exists:preco,id'
    ]);
    Debugbar::info($validated);

    try {
        if ($request->has('id')) {
            $preco = Preco::findOrFail($validated['id']);
        } else {
            $preco = Preco::where([
                ['idDistribuidor', $validated['idDistribuidor']],
                ['idProduto', $validated['idProduto']],
                ['status', '!=', Preco::EXCLUIDO]
            ])
                ->latest('id')
                ->firstOrFail();
        }

        $preco->update([
            'valor' => $validated['valor'],
            'qtdMin' => $validated['qtdMin'],
            'status' => Preco::ATIVO
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Preço atualizado com sucesso!',
            'data' => $preco
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Erro ao atualizar preço!',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\preco  $preco
     * @return \Illuminate\Http\Response
     */
    public function destroy(preco $preco)
    {
        Preco::destroy($preco);
        return;
        //
    }
}
