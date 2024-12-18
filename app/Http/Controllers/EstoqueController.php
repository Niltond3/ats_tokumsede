<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstoqueController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($u = auth()->user()->idDistribuidor) {
            $estoque = Estoque::where('idDistribuidor', $u)->with('produto:id,nome,img')->get();
            return $estoque;
        }else {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }
        //
    }

    function relatorioEstoque(Request $request)
    {
        if (auth()->user()) {
            //Adiciona o filtro do Movimento na SQL dinâmica
            //Verifica se o filtro foi enviado
            if ($request->idDistribuidores!="") {
                //Separa a String oriunda do filtro, obtendo todos os movimentos escolhidos
                $escolhidos = explode(",", $request->idDistribuidores);
                //Contador de movimentos escolhidos
                $contDistribuidor = 0;

                $complementoSql = "";

                //Adiciona todos os movimentos escolhidos à SQL
                for ($i = 0; $i < sizeof($escolhidos); $i++) {

                    //Verifica se já foi adicionado algum movimento à SQL, se sim, adiciona um OR
                    if ($contDistribuidor > 0) {
                        //Adiciona o filtro à SQL
                        $complementoSql = $complementoSql . " or estoque.idDistribuidor = " . $escolhidos[$i] . "";
                        //Incrementa o contador de movimentos
                        $contDistribuidor++;
                    } else {
                        //Adiciona o filtro à SQL
                        $complementoSql = $complementoSql . "estoque.idDistribuidor = " . $escolhidos[$i] . "";
                        //Incrementa o contador de movimentos
                        $contDistribuidor++;
                    }
                }
                $estoques = Estoque::whereRaw($complementoSql)->with('distribuidor:id,nome','produto:id,nome,img,componente')->get();
            }else{
                $estoques = Estoque::with('distribuidor:id,nome','produto:id,nome,img')->get();
            }

            return $estoques;
        }else {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }

        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\estoque  $estoque
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!auth()->user()){
            return response('Sua sessão expirou. Por favor, refaça seu login.', 401);
        }

        $estoque = Estoque::find($id);
		$qtd =  $request->dif;

		if($qtd != 0){
			$this->composicoesArray = array();
			$produto = Produto::find($estoque->idProduto);
			if($qtd > 0){
				$this->atualizaEstoque($estoque->idDistribuidor, $produto, $qtd, true);
			}else{
				$this->atualizaEstoque($estoque->idDistribuidor, $produto, ($qtd * -1), false);
			}
			$this->atualizaComposicoes($estoque->idDistribuidor);
            return response('Estoque atualizado com sucesso.', 200);
		}else{
			return response('Não foi possível alterar o estoque. Valor igual ao estoque anterior.', 400);
		}
    }

}
