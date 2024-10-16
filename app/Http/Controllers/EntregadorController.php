<?php

namespace App\Http\Controllers;

use App\Models\Entregador;
use Illuminate\Http\Request;

class EntregadorController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            $entregadores = auth()->user()->tipoAdministrador == "Distribuidor"?Entregador::where([['status', '!=', 3],['idDistribuidor', auth()->user()->idDistribuidor]])->with('distribuidor:id,nome')->get():Entregador::where('status', '!=', 3)->with('distribuidor:id,nome')->get();
            return $entregadores;
            //LISTA DISTRIBUIDORES
        } else {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }
        //
    }
    public function store(Request $request)
    {
        $request['status'] = Entregador::ATIVO;
        $request['idDistribuidor'] = auth()->user()->idDistribuidor;
        $entregador = new Entregador($request->all());
        $entregador->save();
        return 'Entregador '.$entregador->nome.' cadastrado com Sucesso!';
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\entregador  $entregador
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entregador = Entregador::find($id);
        return $entregador;
        //
    }
        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\entregador  $entregador
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $entregador = Entregador::find($id);
        $entregador->nome = $request->nome;
        $entregador->telefone = $request->telefone;
        $entregador->placaVeiculo = $request->placaVeiculo;
        $entregador->save();
        return $entregador->nome;
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\entregador  $entregador
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entregador = Entregador::findOrFail($id);
        $entregador->status = Entregador::EXCLUIDO;
        $entregador->save();
        return $entregador->nome;
        //EXCLUI ENTREGADOR
    }
}
