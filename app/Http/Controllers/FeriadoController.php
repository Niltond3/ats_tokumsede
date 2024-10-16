<?php

namespace App\Http\Controllers;

use App\Models\Feriado;
use Illuminate\Http\Request;

class FeriadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->tipoAdministrador == "Distribuidor") {
                $feriados = Feriado::where('idDistribuidor', auth()->user()->idDistribuidor)->whereRaw('dataFeriado >= curdate()')->selectRaw("feriado.id as id, date_format(feriado.dataFeriado, '%d/%m/%Y') as dataFeriado")->get();
                return $feriados;
            } else {
                return response('Você não tem acesso a esta funcionalidade.', 400);
            }
        } else {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $feriado = new Feriado();
        $feriado->dataFeriado = $request->dataFeriado == "" ? null : implode("-", array_reverse(explode("/", $request->dataFeriado)));
        $feriado->idDistribuidor = auth()->user()->idDistribuidor;
        $v = Feriado::get()->where('dataFeriado', 'like ',$feriado->dataFeriado)->where('idDistribuidor', $feriado->idDistribuidor);
        if($v->count() == 0 || strcmp($feriado->dataFeriado, "") == 0){
            if ($feriado->save()) {
                return response("Feriado cadastrado com sucesso.", 200);
            } else {
                return response("Erro ao cadastrar o feriado. Tente novamente ou contate o administrador.", 400);
            }
        } else {
            return response('Data de feriado já cadastrada.', 400);
        }
        //CADASTRA FERIADO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\feriado  $feriado
     * @return \Illuminate\Http\Response
     */
    public function destroy($feriado)
    {
        Feriado::destroy($feriado);
        return;
        //
    }
}
