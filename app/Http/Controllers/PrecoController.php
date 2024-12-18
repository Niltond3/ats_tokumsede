<?php

namespace App\Http\Controllers;

use App\Models\Preco;
use App\Models\Estoque;

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
        $produtoId = $request->idProduto;
        $distribuidorId = $request->idDistribuidor;


        $request['inicioValidade'] = null;
        $request['fimValidade'] = null;
        $request['inicioHora']= null;
        $request['fimHora']= null;
        $request['fimHora']= null;
        $request['status'] = Preco::ATIVO;
        $request['idProduto'] = $produtoId;
        $request['idDistribuidor'] = $distribuidorId;
        $request['idCliente'] = $request->idCliente;
        $request['valor'] = $request->valor;
        $request['qtdMin'] = $request->qtdMin;

        $request['idEstoque'] = Estoque::where([["idDistribuidor", $distribuidorId], ["idProduto", $produtoId]])->get()->first();

        $preco = new Preco($request->all());

        if($preco->save()){
            return response()->json(['status' => 'success', 'message' => 'Preço cadastrado com sucesso!']);
        }else{
            return response()->json(['status' => 'error', 'message' => 'Erro ao cadastrar preço!']);
        };
        //
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
        $precos = Preco::where('status','!=', Preco::EXCLUIDO)->where('idDistribuidor',  $distribuidor)->where('idProduto', $idProduto)->with('distribuidor','estoque','produto')
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
    public function update(Request $request, $id)
    {
        if(!$request->status){//*Ativa ou Inativa
            $request['inicioValidade'] = $request->inicioValidade == "" ? null : implode("-", array_reverse(explode("/", $request->inicioValidade)));
            $request['fimValidade'] = $request->fimValidade == "" ? null : implode("-", array_reverse(explode("/", $request->fimValidade)));
        }
        $preco = Preco::find($id);
        $preco->update($request->all());
        return $preco->id;
        //
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
