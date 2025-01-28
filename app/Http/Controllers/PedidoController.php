<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Distribuidor;
use App\Models\Administrador;
use App\Models\EnderecoCliente;
use App\Models\Entregador;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\ItemPedido;
use App\Models\Preco;
use App\Services\FCMNotificationService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\database\query\JoinClause;

date_default_timezone_set('America/Sao_Paulo');

class PedidoController extends Controller
{
    // Core CRUD Operations
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->check()) {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }

        $user = auth()->user();
        $baseQueryCallback = $this->getBaseQueryCallbackStructure($user);
        $queries = $this->buildQueriesArray($baseQueryCallback);

        Debugbar::info($user->tipoAdministrador);

        $this->loadOrderProducts($queries);

        $ultimoPedido = Pedido::orderBy('id', 'DESC')->first();

        $entregadores = Entregador::where("status", Entregador::ATIVO)
    ->when(auth()->user()->tipoAdministrador === 'Distribuidor', function($query) {
        return $query->where('idDistribuidor', auth()->user()->idDistribuidor);
    })
    ->select('id', 'nome')
    ->get();

        return response()->json([
            $queries['pendentes']->orderBy('horarioPedido', 'desc')->get(),
            $queries['aceitos']->orderBy('horarioPedido', 'desc')->get(),
            $queries['despachados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['entregues']->orderBy('horarioPedido', 'desc')->get(),
            $queries['cancelados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['agendados']->orderBy('horarioPedido', 'desc')->get(),
            $ultimoPedido->id,
            $entregadores
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $dataAgendada = $request->dataAgendada == "" ? null : implode("-", array_reverse(explode("/", $request->dataAgendada)));
        //$itens = json_decode($request->itens);

        //*Recupera o id do usuário logado
        $idAdministrador = auth()->user()->id;//$this->escape("user");

        //*Faz o cadastro
        $pedido = new Pedido($request->all());
        $pedido->trocoPara = $request->trocoPara ? $request->trocoPara : 0;
        $pedido->obs = $request->obs ? $request->obs : "";
        $pedido->horarioPedido = date('Y-m-d H:i:s');
        $pedido->status = Pedido::PENDENTE;
        $pedido->origem = $request->origem ? $request->origem : Pedido::PLATAFORMA;
        $pedido->dataAgendada = $dataAgendada;
        $pedido->idAdministrador = $request->origem ? null : $idAdministrador;
        $administradores = Administrador::where([['idDistribuidor', $request->idDistribuidor], ['status', 'Ativo'], ['id', '!=', $idAdministrador]])->orwhere([['tipoAdministrador', 'Administrador'], ['status', 'Ativo'], ['id', '!=', $idAdministrador]])->orwhere([['tipoAdministrador', 'Atendente'], ['status', 'Ativo'], ['id', '!=', $idAdministrador]])->get();
        $endereco = EnderecoCliente::find($request->idEndereco);
        $endereco->update($request->all());
        $cliente = Cliente::find($endereco->idCliente);
        $valorAgua = null;

        //$request->idDistribuidor==23?$pedido->idDistribuidor=14:'';//PASSAR PEDIDOS DA TKS PRAIA PARA O TREZE DE MAIO
        if ($pedido->save()) {

            foreach ($request->itens as $item) {

                //Cria novo objeto referente ao Item
                $itemPedido = new Itempedido();

                //Atribui os valores do formulário ao objeto
                $itemPedido->idPedido = $pedido->id;
                $itemPedido->idProduto = $item['idProduto'];
                $itemPedido->qtd = $item['quantidade'];
                $itemPedido->preco = $item['preco'];
                $itemPedido->subtotal = $item['subtotal'];
                $cliente->precoAcertado ? $itemPedido->precoAcertado = $item['precoAcertado'] : null;

                //PREMIAÇÕES PONTUAÇÃO DO PEDIDO
                // if($premiacoes && $cliente->tipoPessoa==1){
                //     if($itemPedido->idProduto==1){//PL
                //         $pedido->pontuacao+=$itemPedido->qtd;
                //         $valorAgua=$itemPedido->preco;
                //         $aguaPl=true;
                //     }else if($itemPedido->idProduto==4 && !$valorAgua){//RICA
                //         $pedido->pontuacao+=$itemPedido->qtd;
                //         $valorAgua=$itemPedido->preco;
                //     }else if($itemPedido->idProduto==5){//PL+GARRAFAO
                //         $pedido->pontuacao+=$itemPedido->qtd;
                //         $aguaPl=true;
                //     }else if($itemPedido->idProduto==6){//RICA+GARRAFAO
                //         $pedido->pontuacao+=$itemPedido->qtd;
                //         $aguaRica=true;
                //     }
                // }
                //**************************** */
                //Salva o Item
                $itemPedido->save();
            }
            //PREMIAÇÕES DESCONTO NO PEDIDO
            // if($premiacoes && $cliente->tipoPessoa==1){
            //     $pedido->pontuacaoAcumulada=$cliente->pontuacao+$pedido->pontuacao;
            //     if($pedido->pontuacaoAcumulada>=10){
            //         $premios=intval($pedido->pontuacaoAcumulada/10);
            //         if($valorAgua){
            //             $pedido->descontoPremiacao=$premios*$valorAgua;
            //         }else{
            //             //buscar valor
            //             $aguaPl?$preco=Preco::where([['idProduto',1],['idDistribuidor',$request->idDistribuidor],['valor','>',0],['status',Preco::ATIVO],])->first():$preco=Preco::where([['idProduto',4],['idDistribuidor',$request->idDistribuidor],['valor','>',0],['status',Preco::ATIVO],])->first();
            //             $pedido->descontoPremiacao=$premios*$preco->valor;
            //         }
            //         $pedido->total-=$pedido->descontoPremiacao;
            //     }
            //     $pedido->save();
            // }
            //****************************** */
            $fcmService = new FCMNotificationService();

            $msg = [
                'title' => "Pedido {$pedido->id} - {$cliente->nome}",
                'body' => $endereco ? "{$endereco->logradouro} {$endereco->numero}, {$endereco->bairro} - {$endereco->cidade}/{$endereco->estado}" : "Novo pedido recebido",
                'tag' => $pedido->id,
                'icon' => '/images/logo-icon.png',
                'click_action' => 'https://tks.tokumsede.com.br'
            ];

            $fcmService->sendOrderNotification($administradores, $msg);

            return $pedido->id;//return response('Pedido '.$pedido.' cadastrado com sucesso.', 200);
        } else {
            Debugbar::error('Erro ao cadastrar o pedido. Tente novamente ou contate a central');
            return response("Erro ao cadastrar o pedido. Tente novamente ou contate a central.");
        }
        return $itens;
        //CADASTRAR PEDIDO
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pedido = Pedido::find($id);
        $pedido = $this->formatPedidoDates($pedido);
        $pedido->itensPedido = ItemPedido::where('idPedido', $id)->where('qtd', ">", 0)->with('produto')->get();
        $pedido->clientePedido = Cliente::find($pedido->endereco->idCliente);
        return $pedido;
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pedido = Pedido::find($id);
        $pedido->status = $request->status;
        $date = new \DateTime();//**APENAS PARA O ENTREGAR*/
        if ($pedido->horarioEntrega == null) {
            $pedido->horarioEntrega = $date->format('Y-m-d H:i:s');
        }
        $pedido->save();
        return response($pedido->id, 200);
    }

    // Order Status Management
    function setPendente($idPedido)
{
    $date = new \DateTime();
    $pedido = Pedido::find($idPedido);
    $pedido->statusChange = 1;
    $pedido->save();

    $enderecoCliente = Enderecocliente::find($pedido->idEndereco);
    $cliente = Cliente::find($enderecoCliente->idCliente);

    $pedido->status = Pedido::PENDENTE;
    $pedido->editadoPor = auth()->user()->nome;
    $pedido->horarioAceito = null;
    $pedido->horarioDespache = null;
    $pedido->horarioEntrega = null;
    $pedido->horarioCancelado = null;
    $pedido->aceitoPor = null;
    $pedido->despachadoPor = null;
    $pedido->entreguePor = null;
    $pedido->canceladoPor = null;
    $pedido->idEntregador = null;
    $pedido->agendado = 0;
    $pedido->dataAgendada = null;
    $pedido->horaInicio = null;
    $pedido->horaFim = null;

    if ($pedido->save()) {
        if ($cliente->regId != null) {
            $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
        }
        return response($pedido->id, 200);
    }

    return response("Erro ao retornar pedido para pendente. Tente novamente.", 400);
}
    function aceitar(Request $request, $idPedido)
    {
        $date = new \DateTime();
        $pedido = Pedido::find($idPedido);
        if ($pedido->status == Pedido::PENDENTE) {
            $pedido->statusChange = 1;
            // if($request->entregador){
            //     $pedido->idEntregador = $request->entregador;
            // }else{
            //     $pedido->idEntregador = Entregador::where("nome", auth()->user()->nome)->select('id')->first()->id;
            // }
            $enderecoCliente = Enderecocliente::find($pedido->idEndereco);
            $cliente = Cliente::find($enderecoCliente->idCliente);
            $pedido->status = Pedido::ACEITO;
            $pedido->aceitoPor = auth()->user()->nome;
            $pedido->horarioAceito = $date->format('Y-m-d H:i:s');
            if ($pedido->save()) {

                if ($cliente->regId != null) {
                    $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
                }

                return response($pedido->id, 200);//$this->success("Pedido aceito com sucesso.");
            } else {
                return response("Erro ao aceitar o pedido. Tente novamente ou contate o suporte.", 400);//$this->error();
            }
        } else {
            return response("Esse pedido foi cancelado pelo usuário ou já foi aceito por outro administrador!", 400);//$this->error("Esse pedido foi cancelado pelo usuário ou não estava pendente!");
        }

    }
    function despachar(Request $request, $idPedido)
    {
        Debugbar::info($request);
        $date = new \DateTime();
        $pedido = Pedido::find($idPedido);
        if ($pedido->status == Pedido::ACEITO) {
            $pedido->statusChange = 1;
            if ($request->entregador) {
                $pedido->idEntregador = $request->entregador;
            } else {
                $pedido->idEntregador = Entregador::where("nome", auth()->user()->nome)->select('id')->first()->id;
            }
            $pedido->save();
            $enderecoCliente = Enderecocliente::find($pedido->idEndereco);
            $cliente = Cliente::find($enderecoCliente->idCliente);
            $pedido->status = Pedido::DESPACHADO;
            $pedido->despachadoPor = auth()->user()->nome;
            $pedido->horarioDespache = $date->format('Y-m-d H:i:s');
            if ($pedido->save()) {

                if ($cliente->regId != null) {
                    $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
                }

                return response($pedido->id, 200);//$this->success("Pedido aceito com sucesso.");
            } else {
                return response("Erro ao despachar o pedido. Tente novamente ou contate o administrador.", 400);//$this->error();
            }
        } else {
            return response("Esse pedido foi cancelado pelo usuário ou não estava mais como aceito!", 400);//$this->error("Esse pedido foi cancelado pelo usuário ou não estava pendente!");
        }

    }
    function recusar(Request $request, $idPedido)
    {
        $date = new \DateTime();
        $pedido = Pedido::find($idPedido);
        $pedido->statusChange = 1;
        $pedido->save();
        $enderecoCliente = Enderecocliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);
        $pedido->status = Pedido::RECUSADO;
        $pedido->retorno = $request->retorno;
        $pedido->canceladoPor = auth()->user()->nome;
        $pedido->horarioCancelado = $date->format('Y-m-d H:i:s');
        if ($pedido->save()) {

            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }

            return response($pedido->id, 200);//$this->success("Pedido recusado com sucesso.");
        } else {
            return response("Erro ao recusar o pedido. Tente novamente ou contate o administrador.", 400);//$this->error();
        }
    }
    function entregar($idPedido)
    {
        $date = new \DateTime();
        $pedido = Pedido::find($idPedido);
        $pedido->statusChange = 1;
        $pedido->save();
        $distribuidor = Distribuidor::find($pedido->idDistribuidor);
        $itensPedido = ItemPedido::with('produto')->where("idPedido", $idPedido)->get();
        //ALTERA OS ESTOQUES
        if ($pedido->status == Pedido::DESPACHADO) {
            //$itensPedido = ItemPedido::with('produto')->where("idPedido", $idPedido)->get();
            //VERIFICA SE OS PRODUTOS SAO COMPOSICOES OU COMPONENTES ANTES DE ATUALIZAR
            $this->composicoesArray = array();// Zera array
            if ($distribuidor->tipoDistribuidor == "revendedor") {
                foreach ($itensPedido as $itemPedido) {
                    $this->atualizaEstoque($distribuidor->idDistribuidor, $itemPedido->Produto, $itemPedido->qtd, false);
                }
                $this->atualizaComposicoes($distribuidor->idDistribuidor);
            } else {
                foreach ($itensPedido as $itemPedido) {
                    $this->atualizaEstoque($pedido->idDistribuidor, $itemPedido->Produto, $itemPedido->qtd, false);
                }
                $this->atualizaComposicoes($pedido->idDistribuidor);
            }

        }

        $enderecoCliente = Enderecocliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        $cliente->rating = $cliente->rating + 1;

        $pedido->status = Pedido::ENTREGUE;
        $pedido->entreguePor = auth()->user()->nome;
        $pedido->horarioEntrega = $date->format('Y-m-d H:i:s');

        if ($pedido->save()) {
            //PREMIAÇÕES
            // if($premiacoes && $cliente->tipoPessoa==1){
            //     $cliente->pontuacaoAcumulada+=$pedido->pontuacao;
            //     $cliente->pontuacao+=$pedido->pontuacao;
            //     if($cliente->pontuacao>=10){
            //         $distribuidor->premiacoesEntregues+=intval($cliente->pontuacao/10);
            //         $cliente->premios+=intval($cliente->pontuacao/10);
            //         $cliente->pontuacao%=10;
            //         $distribuidor->save();
            //     }
            // }
            //*****
            $cliente->save();
            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }
            response($pedido->id, 200);//$this->success("Pedido entregue com sucesso.");
        } else {
            response("Erro ao entregar o pedido. Tente novamente ou contate o administrador.", 200);//$this->error();
        }
    }
    function cancelar(Request $request, $idPedido)
    {
        $date = new \DateTime();
        $pedido = Pedido::find($idPedido);
        $pedido->statusChange = 1;
        $pedido->save();
        $enderecoCliente = Enderecocliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);

        if ($request->motivo == Pedido::CANCELADO_NAO_LOCALIZADO) {
            $pedido->status = Pedido::CANCELADO_NAO_LOCALIZADO;
            $cliente->rating = $cliente->rating - 1;
        } else if ($request->motivo == Pedido::CANCELADO_TROTE) {
            $pedido->status = Pedido::CANCELADO_TROTE;
            $cliente->rating = $cliente->rating - 3;
        }
        $pedido->horarioCancelado = $date->format('Y-m-d H:i:s');
        $pedido->canceladoPor = auth()->user()->nome;
        if ($pedido->save()) {
            $cliente->save();

            if ($cliente->regId != null) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }

            return response($pedido->id, 200);//$this->success("Pedido cancelado com sucesso.");
        } else {
            return response("Erro ao cancelar o pedido. Tente novamente ou contate o administrador.", 400);//$this->error();
        }
    }

    // Report Methods
    function relatorioVendas(Request $request)
    {
        $u = auth()->user();//Administrador::find($idUsuario);
        $request['dataInicial'] = implode("-", array_reverse(explode("/", $request->dataInicial)));
        $request['dataFinal'] = implode("-", array_reverse(explode("/", $request->dataFinal)));

        $filtroData = "";
        if ($request->dataInicial != null && $request->dataInicial != "") {
            $filtroData .= "and pedido.horarioEntrega >= '$request->dataInicial 00:00:00'";
        }
        if ($request->dataFinal != null && $request->dataFinal != "") {
            $filtroData .= "and pedido.horarioEntrega <= '$request->dataFinal 23:59:59'";
        }

        //Variável que irá armazenar o conteúdo dinâmico da SQL
        $complementoSql = "";

        //Adiciona o filtro do Movimento na SQL dinâmica
        //Verifica se o filtro foi enviado
        if ($request->idDistribuidores != "" && $request->idDistribuidores != null) {
            //Separa a String oriunda do filtro, obtendo todos os movimentos escolhidos
            $escolhidos = explode(",", $request->idDistribuidores);
            //Contador de movimentos escolhidos
            $contDistribuidor = 0;

            $complementoSql = $complementoSql . " and (";

            //Adiciona todos os movimentos escolhidos à SQL
            for ($i = 0; $i < sizeof($escolhidos); $i++) {

                //Verifica se já foi adicionado algum movimento à SQL, se sim, adiciona um OR
                if ($contDistribuidor > 0) {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . " or pedido.idDistribuidor = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contDistribuidor++;
                } else {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . "pedido.idDistribuidor = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contDistribuidor++;
                }
            }

            $complementoSql = $complementoSql . ") ";
        }

        if (strcmp($u->tipoAdministrador, "Distribuidor") == 0) {
            $pedidos = DB::table('pedido')->join('distribuidor', 'pedido.idDistribuidor', '=', 'distribuidor.id')
                ->select(DB::raw("pedido.idDistribuidor as idDist, distribuidor.nome as distribuidor, date_format(pedido.horarioEntrega, '%d/%m/%Y') as dataEntrega, sum(pedido.total) as valorTotal"))
                ->whereRaw("pedido.status = 7 $filtroData $complementoSql and pedido.idDistribuidor = " . $u->idDistribuidor . " ")
                ->groupBy('idDist', 'dataEntrega', 'distribuidor')
                ->get();
        } else {
            $pedidos = DB::table('pedido')->join('distribuidor', 'pedido.idDistribuidor', '=', 'distribuidor.id')
                ->select(DB::raw("pedido.idDistribuidor as idDist, distribuidor.nome as distribuidor, date_format(pedido.horarioEntrega, '%d/%m/%Y') as dataEntrega, sum(pedido.total) as valorTotal"))
                ->whereRaw("pedido.status = 7 $filtroData $complementoSql")
                ->groupBy('idDist', 'dataEntrega', 'distribuidor')
                ->get();
        }

        $valorTotal = 0;

        $valorTotalGeral = 0;
        foreach ($pedidos as $pedido) {
            setlocale(LC_MONETARY, "pt_BR", "ptb");
            $valorTotalGeral += $pedido->valorTotal;
            $pedido->valorTotal = 'R$ ' . number_format($pedido->valorTotal, 2, ',', '.');
        }
        // for ($i = 0; $i < sizeof($pedidos); $i++) {
        //     setlocale(LC_MONETARY, "pt_BR", "ptb");
        //     $valorTotalGeral += $pedidos[$i]->valorTotal;
        //     $pedidos[$i]->valorTotal = 'R$ ' . number_format($pedidos[$i]->valorTotal, 2, ',', '.');
        // }
        $valorTotalGeral = 'R$ ' . number_format($valorTotalGeral, 2, ',', '.');

        return [$pedidos, $valorTotalGeral];
    }
    function relatorioVendasProduto(Request $request)
    {
        $u = auth()->user();
        $request['dataInicial'] = implode("-", array_reverse(explode("/", $request->dataInicial)));
        $request['dataFinal'] = implode("-", array_reverse(explode("/", $request->dataFinal)));

        $filtroData = "";
        if ($request->dataInicial != null && $request->dataInicial != "") {
            $filtroData .= "and pedido.horarioEntrega >= '$request->dataInicial 00:00:00'";
        }
        if ($request->dataFinal != null && $request->dataFinal != "") {
            $filtroData .= $filtroData == "" ? " pedido.horarioEntrega <= '$request->dataFinal 23:59:59'" : "AND pedido.horarioEntrega <= '$request->dataFinal 23:59:59'";
        }

        //Variável que irá armazenar o conteúdo dinâmico da SQL
        $complementoSql = "";

        //Adiciona o filtro do Movimento na SQL dinâmica
        //Verifica se o filtro foi enviado
        if ($request->idDistribuidores != "" && $request->idDistribuidores != null) {
            //Separa a String oriunda do filtro, obtendo todos os movimentos escolhidos
            $escolhidos = explode(",", $request->idDistribuidores);
            //Contador de movimentos escolhidos
            $contDistribuidor = 0;

            $complementoSql = $complementoSql . " and (";

            //Adiciona todos os movimentos escolhidos à SQL
            for ($i = 0; $i < sizeof($escolhidos); $i++) {

                //Verifica se já foi adicionado algum movimento à SQL, se sim, adiciona um OR
                if ($contDistribuidor > 0) {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . " or pedido.idDistribuidor = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contDistribuidor++;
                } else {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . "pedido.idDistribuidor = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contDistribuidor++;
                }
            }

            $complementoSql = $complementoSql . ") ";
        }

        if ($request->selectProdutos != "") {
            //Separa a String oriunda do filtro, obtendo todos os movimentos escolhidos
            $escolhidos = explode(",", $request->selectProdutos);
            //Contador de movimentos escolhidos
            $contProdutos = 0;

            $complementoSql = $complementoSql . " and (";

            //Adiciona todos os movimentos escolhidos à SQL
            for ($i = 0; $i < sizeof($escolhidos); $i++) {

                //Verifica se já foi adicionado algum movimento à SQL, se sim, adiciona um OR
                if ($contProdutos > 0) {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . " or itemPedido.idProduto = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contProdutos++;
                } else {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . "itemPedido.idProduto = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contProdutos++;
                }
            }

            $complementoSql = $complementoSql . ") ";
        }
        if (strcmp($u->tipoAdministrador, "Distribuidor") == 0) {
            $pedidos = DB::table('pedido')
                ->from(DB::raw("((itemPedido itemPedido left join pedido pedido on itemPedido.idPedido = pedido.id)left join distribuidor distribuidor on pedido.idDistribuidor = distribuidor.id)left join produto produto on itemPedido.idProduto = produto.id"))
                ->select(DB::raw("itemPedido.idProduto as idProd, produto.nome as produto, distribuidor.nome as distribuidor, date_format(pedido.horarioEntrega, '%d/%m/%Y') as dataEntrega, sum(itemPedido.subtotal) as valorTotal, sum(itemPedido.qtd) as quantidadeTotal"))
                ->whereRaw("pedido.status = 7 $filtroData $complementoSql and pedido.idDistribuidor = " . $u->idDistribuidor . " ")
                ->groupBy('idProd', 'dataEntrega', 'distribuidor', 'produto')
                ->get();
        } else {
            $pedidos = DB::table('pedido')
                ->from(DB::raw("((itemPedido itemPedido left join pedido pedido on itemPedido.idPedido = pedido.id)left join distribuidor distribuidor on pedido.idDistribuidor = distribuidor.id)left join produto produto on itemPedido.idProduto = produto.id"))
                ->select(DB::raw("itemPedido.idProduto as idProd, produto.nome as produto, distribuidor.nome as distribuidor, date_format(pedido.horarioEntrega, '%d/%m/%Y') as dataEntrega, sum(itemPedido.subtotal) as valorTotal, sum(itemPedido.qtd) as quantidadeTotal"))
                ->whereRaw("pedido.status = 7 $filtroData $complementoSql")
                ->groupBy('idProd', 'dataEntrega', 'distribuidor', 'produto')
                ->get();
        }
        $valorTotal = 0;

        $valorTotalGeral = 0;
        foreach ($pedidos as $pedido) {
            setlocale(LC_MONETARY, "pt_BR", "ptb");
            $valorTotalGeral += $pedido->valorTotal;
            $pedido->valorTotal = 'R$ ' . number_format($pedido->valorTotal, 2, ',', '.');
        }
        $valorTotalGeral = 'R$ ' . number_format($valorTotalGeral, 2, ',', '.');

        return [$pedidos, $valorTotalGeral];
    }
    function relatorioVendasEntregador(Request $request)
    {
        $u = auth()->user();
        $request['dataInicial'] = implode("-", array_reverse(explode("/", $request->dataInicial)));
        $request['dataFinal'] = implode("-", array_reverse(explode("/", $request->dataFinal)));

        $filtroData = "";
        if ($request->dataInicial != null && $request->dataInicial != "") {
            $filtroData .= "and pedido.horarioEntrega >= '$request->dataInicial 00:00:00'";
        }
        if ($request->dataFinal != null && $request->dataFinal != "") {
            $filtroData .= $filtroData == "" ? " pedido.horarioEntrega <= '$request->dataFinal 23:59:59'" : "AND pedido.horarioEntrega <= '$request->dataFinal 23:59:59'";
        }

        //Variável que irá armazenar o conteúdo dinâmico da SQL
        $complementoSql = "";

        //Adiciona o filtro do Movimento na SQL dinâmica
        //Verifica se o filtro foi enviado
        if ($request->idEntregadores != "" && $request->idEntregadores != null) {
            //Separa a String oriunda do filtro, obtendo todos os movimentos escolhidos
            $escolhidos = explode(",", $request->idEntregadores);
            //Contador de movimentos escolhidos
            $contEntregador = 0;

            $complementoSql = $complementoSql . " and (";

            //Adiciona todos os movimentos escolhidos à SQL
            for ($i = 0; $i < sizeof($escolhidos); $i++) {

                //Verifica se já foi adicionado algum movimento à SQL, se sim, adiciona um OR
                if ($contEntregador > 0) {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . " or pedido.idEntregador = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contEntregador++;
                } else {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . "pedido.idEntregador = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contEntregador++;
                }
            }

            $complementoSql = $complementoSql . ") ";
        }

        if ($request->selectProdutos != "") {
            //Separa a String oriunda do filtro, obtendo todos os movimentos escolhidos
            $escolhidos = explode(",", $request->selectProdutos);
            //Contador de movimentos escolhidos
            $contProdutos = 0;

            $complementoSql = $complementoSql . " and (";

            //Adiciona todos os movimentos escolhidos à SQL
            for ($i = 0; $i < sizeof($escolhidos); $i++) {

                //Verifica se já foi adicionado algum movimento à SQL, se sim, adiciona um OR
                if ($contProdutos > 0) {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . " or itemPedido.idProduto = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contProdutos++;
                } else {
                    //Adiciona o filtro à SQL
                    $complementoSql = $complementoSql . "itemPedido.idProduto = " . $escolhidos[$i] . "";
                    //Incrementa o contador de movimentos
                    $contProdutos++;
                }
            }

            $complementoSql = $complementoSql . ") ";
        }
        if (strcmp($u->tipoAdministrador, "Distribuidor") == 0) {
            $pedidos = DB::table('pedido')
                ->from(DB::raw("(((itemPedido itemPedido left join pedido pedido on itemPedido.idPedido = pedido.id)left join distribuidor distribuidor on pedido.idDistribuidor = distribuidor.id)left join entregador entregador on pedido.idEntregador = entregador.id)left join produto produto on itemPedido.idProduto = produto.id"))
                ->select(DB::raw("itemPedido.idProduto as idProd, itemPedido.precoAcertado as precoAcertado, produto.nome as produto, distribuidor.nome as distribuidor, entregador.nome as entregador, sum(itemPedido.subtotal) as valorTotal, sum(itemPedido.qtd) as quantidadeTotal"))
                ->whereRaw("pedido.status = 7 $filtroData $complementoSql and pedido.idDistribuidor = " . $u->idDistribuidor . " ")
                ->groupBy('idProd', 'distribuidor', 'entregador', 'produto', 'precoAcertado')
                ->get();
        } else {
            $pedidos = DB::table('pedido')
                ->from(DB::raw("(((itemPedido itemPedido left join pedido pedido on itemPedido.idPedido = pedido.id)left join distribuidor distribuidor on pedido.idDistribuidor = distribuidor.id)left join entregador entregador on pedido.idEntregador = entregador.id)left join produto produto on itemPedido.idProduto = produto.id"))
                ->select(DB::raw("itemPedido.idProduto as idProd, itemPedido.precoAcertado as precoAcertado, produto.nome as produto, distribuidor.nome as distribuidor, entregador.nome as entregador, sum(itemPedido.subtotal) as valorTotal, sum(itemPedido.qtd) as quantidadeTotal"))
                ->whereRaw("pedido.status = 7 $filtroData $complementoSql")
                ->groupBy('idProd', 'distribuidor', 'entregador', 'produto', 'precoAcertado')
                ->get();
        }
        $valorTotal = 0;

        $valorTotalGeral = 0;
        foreach ($pedidos as $pedido) {
            setlocale(LC_MONETARY, "pt_BR", "ptb");
            $valorTotalGeral += $pedido->valorTotal;
            $pedido->valorTotal = 'R$ ' . number_format($pedido->valorTotal, 2, ',', '.');
        }
        $valorTotalGeral = 'R$ ' . number_format($valorTotalGeral, 2, ',', '.');

        return [$pedidos, $valorTotalGeral];
    }
    public function relatorioPedidos(Request $request)
    {
        if (!auth()->check()) {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }

        $queries = [
            'pendentes' => Pedido::withBasicRelations()->withFormattedDates()
            ->whereRaw("((pedido.agendado = 1 AND (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) <= 30)
            OR DATE(pedido.dataAgendada) < CURDATE()) OR pedido.agendado = 0)")
            ->where('status', Pedido::PENDENTE),
            'aceitos' => Pedido::withBasicRelations()->withFormattedDates()->where('status', Pedido::ACEITO),
            'despachados' => Pedido::withBasicRelations()->withFormattedDates()->where('status', Pedido::DESPACHADO),
            'entregues' => Pedido::withBasicRelations()->withFormattedDates()->where('status', Pedido::ENTREGUE),
            'cancelados' => Pedido::withBasicRelations()->withFormattedDates()->whereIn('status', [
                Pedido::CANCELADO_USUARIO,
                Pedido::CANCELADO_NAO_LOCALIZADO,
                Pedido::CANCELADO_TROTE,
                Pedido::RECUSADO
            ]),
            'agendados' => Pedido::withBasicRelations()->withFormattedDates()
            ->where([
                ['status', Pedido::PENDENTE],
                ['agendado', 1]
            ])
            ->whereRaw("DATE(pedido.dataAgendada) > CURDATE() OR (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) > 30)")
        ];

        $this->applyDateFilters($queries, $request);
        $this->applyDistribuidorFilter($queries, $request);
        $this->loadOrderProducts($queries);

        $ultimoPedido = Pedido::orderBy('id', 'DESC')->first();
        $entregadores = Entregador::where("status", Entregador::ATIVO)
    ->select('id', 'nome')
    ->get();

        return [
            $queries['pendentes']->orderBy('horarioPedido', 'desc')->get(),
            $queries['aceitos']->orderBy('horarioPedido', 'desc')->get(),
            $queries['despachados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['entregues']->orderBy('horarioPedido', 'desc')->get(),
            $queries['cancelados']->orderBy('horarioPedido', 'desc')->get(),
            $queries['agendados']->orderBy('horarioPedido', 'desc')->get(),
            $ultimoPedido->id,
            $entregadores
        ];
    }
    // Helper Methods
    private function applyDateFilters(&$queries, $request)
    {
        if ($request->dataInicial) {
            $dataInicial = implode("-", array_reverse(explode("/", $request->dataInicial)));
            $whereClause = "horarioPedido >= '$dataInicial 00:00:00'";
            foreach ($queries as $query) {
                $query->whereRaw($whereClause);
            }
        }

        if ($request->dataFinal) {
            $dataFinal = implode("-", array_reverse(explode("/", $request->dataFinal)));
            $whereClause = "horarioPedido <= '$dataFinal 23:59:59'";
            foreach ($queries as $query) {
                $query->whereRaw($whereClause);
            }
        }
    }
    private function applyDistribuidorFilter(&$queries, $request)
    {
        if ($request->idDistribuidores) {
            $distribuidores = explode(',', $request->idDistribuidores);
            foreach ($queries as $query) {
                $query->whereIn('idDistribuidor', $distribuidores);
            }
        }
    }
    private function loadOrderProducts(&$queries)
    {
        foreach ($queries as $query) {
            $query->get()->map(function ($pedido) {
                $pedido = $this->formatPedidoDates($pedido);
                $pedido->produtos = $this->formatProductsOutput(
                    $pedido->itens->map(function ($item) {
                        return (object) [
                            'id' => $item->id,
                            'idProd' => $item->produto->id,
                            'nome' => $item->produto->nome,
                            'img' => $item->produto->img,
                            'qtdMin' => $item->qtd,
                            'valor' => $item->preco
                        ];
                    })->toArray()
                );
                unset($pedido->itens);
                return $pedido;
            });
        }
    }
    private function formatProductsOutput($produtos)
    {
        if (empty($produtos[0]->idProd)) {
            return [];
        }

        $out = [];
        $currentProduct = null;
        $indexProduto = -1;

        foreach ($produtos as $prod) {
            if ($currentProduct !== $prod->idProd) {
                $indexProduto++;
                $currentProduct = $prod->idProd;

                $out[$indexProduto] = [
                    "nome" => $prod->nome,
                    "id" => $prod->idProd,
                    "img" => $prod->img,
                    "preco" => [],
                    "precoEspecial" => [],
                ];
            }

            $out[$indexProduto]['preco'][] = [
                'precoId' => $prod->id,
                'qtd' => $prod->qtdMin,
                'val' => $prod->valor
            ];

            $out[$indexProduto]['preco'] = $this->sortPriceArrayByPrecoId($out[$indexProduto]['preco']);

            if (!empty($out[$indexProduto]['precoEspecial'])) {
                $out[$indexProduto]['precoEspecial'] = $this->sortPriceArrayByPrecoId($out[$indexProduto]['precoEspecial']);
            } else {
                unset($out[$indexProduto]['precoEspecial']);
            }
        }

        return $out;
    }
    private function sortPriceArrayByPrecoId(array $prices): array
    {
        usort($prices, function ($a, $b) {
            return $a['precoId'] - $b['precoId'];
        });
        return $prices;
    }
    private function formatDate($date) {
        return date('d/m/Y', strtotime($date));
    }
    private function formatDateTime($datetime) {
        return date('d/m/Y H:i', strtotime($datetime));
    }
    private function formatPedidoDates($pedido) {
        $pedido->horarioPedido = $this->formatDateTime($pedido->horarioPedido);
        $pedido->horarioAceito = $this->formatDateTime($pedido->horarioAceito);
        $pedido->horarioDespache = $this->formatDateTime($pedido->horarioDespache);
        $pedido->horarioEntrega = $this->formatDateTime($pedido->horarioEntrega);
        $pedido->dataAgendada = $this->formatDateTime($pedido->dataAgendada);
        return $pedido;
    }
    private function getFormattedPedidoWithProducts($query)
    {
        return $query->with([
            'distribuidor',
            'endereco',
            'entregador',
            'itens' => function ($q) {
                $q->where('qtd', '>', 0)->with('produto');
            }
        ])
            ->get()
            ->map(function ($pedido) {
                $pedido->produtos = $this->formatProductsOutput(
                    $pedido->itens->map(function ($item) {
                        return (object) [
                            'id' => $item->id,
                            'idProd' => $item->produto->id,
                            'nome' => $item->produto->nome,
                            'img' => $item->produto->img,
                            'qtdMin' => $item->qtd,
                            'valor' => $item->preco
                        ];
                    })->toArray()
                );
                unset($pedido->itens);
                return $pedido;
            });
    }
    private function getBaseQueryCallbackStructure($user) {
        return function() use ($user) {
            $query = Pedido::withBasicRelations();


            if ($user->tipoAdministrador === null) {
                // Cliente queries
                $query->join('enderecoCliente', 'pedido.idEndereco', 'enderecoCliente.id')
                    ->where('enderecoCliente.idCliente', $user->id);
            } else if (in_array($user->tipoAdministrador, ['Administrador', 'Atendente'])) {
                // Admin/Atendente - no additional filters
            } else {
                // Distribuidor/Entregador queries
                $query->where('idDistribuidor', $user->idDistribuidor);
            }
            return $query;
        };
    }
    private function buildQueriesArray($baseQueryCallback) {
        return [
            'pendentes' => clone $baseQueryCallback()
                ->where('status', Pedido::PENDENTE)
                ->whereRaw("((pedido.agendado = 1 AND (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) <= 30) OR DATE(pedido.dataAgendada) < CURDATE()) OR pedido.agendado = 0)"),
            'aceitos' => clone $baseQueryCallback()
                ->where('status', Pedido::ACEITO),
            'despachados' => clone $baseQueryCallback()
                ->where('status', Pedido::DESPACHADO),
            'entregues' => clone $baseQueryCallback()
                ->where('status', Pedido::ENTREGUE)
                ->whereRaw("DATE(pedido.horarioEntrega) = CURDATE()"),
            'cancelados' => clone $baseQueryCallback()
                ->whereIn('status', [
                    Pedido::CANCELADO_USUARIO,
                    Pedido::CANCELADO_NAO_LOCALIZADO,
                    Pedido::CANCELADO_TROTE,
                    Pedido::RECUSADO
                ])
                ->whereRaw("DATE(pedido.horarioPedido) = CURDATE()"),
            'agendados' => clone $baseQueryCallback()
                ->where([
                    ['status', Pedido::PENDENTE],
                    ['agendado', 1]
                ])
                ->whereRaw("DATE(pedido.dataAgendada) > CURDATE() OR (DATE(pedido.dataAgendada) = CURDATE() AND ((pedido.horaInicio - CURTIME())/100) > 30)")
        ];
    }
    function notification($msg, $administradores)
    {
        $fcmService = new FCMNotificationService();
        $fcmService->sendOrderNotification($administradores, $msg);
    }
    function buscarNovosPedidos($ultimoPedido)
    {
        $novosPedidos = '';
        $u = auth()->user();//Administrador::find($idUsuario);
        if (auth()->check()) {
            if (strcmp($u->tipoAdministrador, "Administrador") == 0) {
                $novosPedidos = Pedido::select("pedido.*") // Seleciona os campos
                    ->whereRaw("pedido.status = " . Pedido::PENDENTE . " and pedido.id > " . $ultimoPedido . " and ((pedido.agendado = 1 and (DATE(pedido.dataAgendada) = CURDATE() and ((pedido.horaInicio - CURTIME())/100) <= 30) or DATE(pedido.dataAgendada) < CURDATE()) or pedido.agendado = 0)")
                    ->get();
            } else {
                $novosPedidos = Pedido::select("pedido.*") // Seleciona os campos
                    ->leftJoin('distribuidor', 'distribuidor.id', '=', 'pedido.idDistribuidor')
                    ->whereRaw("pedido.status = " . Pedido::PENDENTE . " and pedido.id > " . $ultimoPedido . " and pedido.idDistribuidor = " . $u->idDistribuidor .
                        " and ((pedido.agendado = 1 and (DATE(pedido.dataAgendada) = CURDATE() and ((pedido.horaInicio - CURTIME())/100) <= 30) or DATE(pedido.dataAgendada) < CURDATE()) or pedido.agendado = 0)")
                    ->get();
            }
        }
        if ($novosPedidos != '') {
            return $novosPedidos;
        }
    }
    function ultimoPedido()
    {
        $u = auth()->user();
        if (strcmp($u->tipoAdministrador, "Administrador") == 0 || strcmp($u->tipoAdministrador, "Atendente") == 0) {
            $ultimoPedido = Pedido::orderBy("pedido.id", "DESC")
                ->limit(1)
                ->get();
        } else {
            $ultimoPedido = Pedido::where('idDistribuidor', $u->idDistribuidor)
                ->orderBy("pedido.id", "DESC")
                ->limit(1)
                ->get();
        }
        return $ultimoPedido[0]->id;
    }
    public function atualizar(Request $request, $idPedido)
    {
        $request['dataAgendada'] = $request->dataAgendada == "" ? null : implode("-", array_reverse(explode("/", $request->dataAgendada)));
        $request['trocoPara'] = $request->trocoPara ? $request->trocoPara : 0;
        $pedido = Pedido::find($idPedido);
        $enderecoCliente = Enderecocliente::find($pedido->idEndereco);
        $cliente = Cliente::find($enderecoCliente->idCliente);
        $distribuidor = Distribuidor::find($pedido->idDistribuidor);
        $itensPedido = ItemPedido::with('produto')->where("idPedido", $idPedido)->get();
        if ($pedido->status == Pedido::ENTREGUE) {
            //somar quantidades ao estoque do distribuidor pra depois subtrair novamente caso pedido continue em entregue
            //VERIFICA SE OS PRODUTOS SAO COMPOSICOES OU COMPONENTES ANTES DE ATUALIZAR
            $this->composicoesArray = array();// Zera array
            if ($distribuidor->tipoDistribuidor == "revendedor") {
                foreach ($itensPedido as $itemPedido) {
                    $this->atualizaEstoque($distribuidor->idDistribuidor, $itemPedido->Produto, $itemPedido->qtd, true);
                }
                $this->atualizaComposicoes($distribuidor->idDistribuidor);
            } else {
                foreach ($itensPedido as $itemPedido) {
                    $this->atualizaEstoque($pedido->idDistribuidor, $itemPedido->Produto, $itemPedido->qtd, true);
                }
                $this->atualizaComposicoes($pedido->idDistribuidor);
            }
        }
        if ($request->idDistribuidor != $pedido->idDistribuidor) {
            if ($pedido->status == Pedido::ENTREGUE) {
                $cliente->rating--;
                $cliente->save();
            } else if ($pedido->status == Pedido::CANCELADO_NAO_LOCALIZADO) {
                $cliente->rating++;
                $cliente->save();
            } else if ($pedido->status == Pedido::CANCELADO_TROTE) {
                $cliente->rating += 3;
                $cliente->save();
            }
            $pedido->status = Pedido::PENDENTE;
            $administradores = Administrador::where('idDistribuidor', $pedido->idDistribuidor)->orwhere('idDistribuidor', $request->idDistribuidor)->orwhere('tipoAdministrador', 'Administrador');
            $msg = array(
                'title' => 'Distribuidor Alterado: ' . $pedido->id . ' - ' . $cliente->nome,
                'body' => '[Distribuição ' . $pedido->idDistribuidor . ' para ' . $request->idDistribuidor . '] ' . $enderecoCliente->logradouro . ' ' . $enderecoCliente->numero . ', ' . $enderecoCliente->bairro . ' - ' . $enderecoCliente->cidade . '/' . $enderecoCliente->estado,
                // 'tag' => $pedido->id,
                'icon' => '/images/logo-icon.png',
                'click_action' => 'https://adm.tokumsede.com'
            );
        } else if ($request->status != $pedido->status) {
            $administradores = Administrador::where('idDistribuidor', $pedido->idDistribuidor)->orwhere('tipoAdministrador', 'Administrador');
            $msg = array(
                'title' => 'Status Alterado: ' . $pedido->id . ' - ' . $cliente->nome,
                'body' => $enderecoCliente->logradouro . ' ' . $enderecoCliente->numero . ', ' . $enderecoCliente->bairro . ' - ' . $enderecoCliente->cidade . '/' . $enderecoCliente->estado,
                // 'tag' => $pedido->id,
                'icon' => '/images/logo-icon.png',
                'click_action' => 'https://adm.tokumsede.com'
            );
        } else {
            //*Recupera o id do usuário logado

        }
        $idAdministrador = auth()->user()->id;//$this->escape("user");
        $administradores = Administrador::where([['idDistribuidor', $request->idDistribuidor], ['status', 'Ativo'], ['id', '!=', $idAdministrador]])->orwhere([['tipoAdministrador', 'Administrador'], ['status', 'Ativo'], ['id', '!=', $idAdministrador]])->orwhere([['tipoAdministrador', 'Atendente'], ['status', 'Ativo'], ['id', '!=', $idAdministrador]])->get();

        // $administradores = Administrador::where('idDistribuidor', $pedido->idDistribuidor)->orwhere('tipoAdministrador', 'Administrador');
        $msg = array(
            'title' => 'Pedido Alterado: ' . $pedido->id . ' - ' . $cliente->nome,
            'body' => $enderecoCliente->logradouro . ' ' . $enderecoCliente->numero . ', ' . $enderecoCliente->bairro . ' - ' . $enderecoCliente->cidade . '/' . $enderecoCliente->estado,
            // 'tag' => $pedido->id,
            'icon' => '/images/logo-icon.png',
            'click_action' => 'https://adm.tokumsede.com'
        );

        $fcmService = new FCMNotificationService();
        $fcmService->sendOrderNotification($administradores, $msg);

        $pedido->editadoPor = auth()->user()->nome;
        if ($pedido->update($request->all())) {
            //Zerar todos os itens do pedido
            $itensPedido = Itempedido::where('idPedido', $idPedido)->get();
            foreach ($itensPedido as $item) {
                $item->qtd = 0;
                $item->preco = 0;
                $item->subtotal = 0;
                //Salva o Item
                $item->save();
            }

            //update cada
            foreach ($request->itens as $item) {
                $itemPedido = Itempedido::where([['idPedido', $idPedido], ['idProduto', $item['idProduto']]])->first();
                if ($itemPedido) {
                    //Atribui os valores do formulário ao objeto
                    $itemPedido->qtd = $item['quantidade'];
                    $itemPedido->preco = $item['preco'];
                    $itemPedido->subtotal = $item['subtotal'];

                    //Salva o Item
                    $itemPedido->save();
                } else {//add os que não forem encontrados
                    //Cria novo objeto referente ao Item
                    $itemPedido = new Itempedido();
                    //Atribui os valores do formulário ao objeto
                    $itemPedido->idPedido = $idPedido;
                    $itemPedido->idProduto = $item['idProduto'];
                    $itemPedido->qtd = $item['quantidade'];
                    $itemPedido->preco = $item['preco'];
                    $itemPedido->subtotal = $item['subtotal'];

                    //Salva o Item
                    $itemPedido->save();
                }
            }
            $itensPedido = ItemPedido::with('produto')->where("idPedido", $idPedido)->get();
            if ($pedido->status == Pedido::ENTREGUE) {
                //subtrair quantidades ao estoque do distribuidor caso depido ainda esteja como entregue
                //VERIFICA SE OS PRODUTOS SAO COMPOSICOES OU COMPONENTES ANTES DE ATUALIZAR
                $this->composicoesArray = array();// Zera array
                if ($distribuidor->tipoDistribuidor == "revendedor") {
                    foreach ($itensPedido as $itemPedido) {
                        $this->atualizaEstoque($distribuidor->idDistribuidor, $itemPedido->Produto, $itemPedido->qtd, false);
                    }
                    $this->atualizaComposicoes($distribuidor->idDistribuidor);
                } else {
                    foreach ($itensPedido as $itemPedido) {
                        $this->atualizaEstoque($pedido->idDistribuidor, $itemPedido->Produto, $itemPedido->qtd, false);
                    }
                    $this->atualizaComposicoes($pedido->idDistribuidor);
                }
            }
            //Notificações Firebase
            $this->notification($msg, $administradores);
            if ($cliente->regId != null && $request->status != $pedido->status) {
                $this->gcmSend($cliente->regId, $cliente->id, $idPedido, $pedido->status, $pedido->retorno, $pedido->origem, true);
            }
            return $pedido->id;//return response('Pedido '.$pedido.' cadastrado com sucesso.', 200);
        } else {
            return response("Erro ao atualizar o pedido. Tente novamente ou contate o suporte.");
        }
    }
    public function visualizar($id)
    {
        $pedido = Pedido::selectRaw("pedido.*, CONCAT('', REPLACE(REPLACE(REPLACE(FORMAT( (pedido.trocoPara - pedido.total) , 2),'.',';'),',','.'),';',',')) AS troco, date_format(pedido.horarioPedido, '%d/%m/%Y %H:%i') as horarioPedido, date_format(pedido.horarioAceito, '%d/%m/%Y %H:%i') as horarioAceito, date_format(pedido.horarioDespache, '%d/%m/%Y %H:%i') as horarioDespache, date_format(pedido.horarioEntrega, '%d/%m/%Y %H:%i') as horarioEntrega, date_format(pedido.dataAgendada, '%d/%m/%Y') as dataAgendada")->find($id);
        $u = auth()->user();
        if ($u->tipoAdministrador == 'Distribuidor' && $u->idDistribuidor != $pedido->idDistribuidor) {
            return false;
        }
        $pedido->itensPedido = ItemPedido::where('idPedido', $id)->where('qtd', "!=", 0)->with('produto')->get();
        $pedido->clientePedido = Cliente::find($pedido->endereco->idCliente);
        $pedido->distribuidor = Distribuidor::select('distribuidor.nome', 'distribuidor.dddTelefone', 'distribuidor.telefonePrincipal')->find($pedido->idDistribuidor);
        $pedido->entregador = Entregador::select('nome', 'telefone')->find($pedido->idEntregador);
        if ($pedido->idAdministrador != null) {
            $administrador = Administrador::select('nome')->find($pedido->idAdministrador);
            $pedido->administrador = $administrador->nome;
        }
        return $pedido;
        //
    }
    public function editar($id)
    {
        $pedido = Pedido::selectRaw("pedido.*, CONCAT('', REPLACE(REPLACE(REPLACE(FORMAT( (pedido.trocoPara - pedido.total) , 2),'.',';'),',','.'),';',',')) AS troco, date_format(pedido.horarioPedido, '%d/%m/%Y %H:%i') as horarioPedido, date_format(pedido.horarioAceito, '%d/%m/%Y %H:%i') as horarioAceito, date_format(pedido.horarioDespache, '%d/%m/%Y %H:%i') as horarioDespache, date_format(pedido.horarioEntrega, '%d/%m/%Y %H:%i') as horarioEntrega, date_format(pedido.dataAgendada, '%d/%m/%Y') as dataAgendada")->find($id);
        $u = auth()->user();
        if ($u->tipoAdministrador != 'Administrador' && $u->id != $pedido->idAdministrador && $u->id != 50 && $u->id != 51 && $u->id != 61 && $u->id != 48 && $u->id != 96) {
            return false;
        }
        $pedido->itensPedido = ItemPedido::where('idPedido', $id)->where('qtd', "!=", 0)->with('produto')->get();
        $pedido->clientePedido = Cliente::find($pedido->endereco->idCliente);
        $pedido->distribuidor = Distribuidor::select('distribuidor.id', 'distribuidor.nome', 'distribuidor.dddTelefone', 'distribuidor.telefonePrincipal')->find($pedido->idDistribuidor);
        $pedido->entregador = Entregador::select('nome', 'telefone')->find($pedido->idEntregador);
        $distribuidores = Distribuidor::where('status', '!=', 3)->get();
        if ($pedido->idAdministrador != null) {
            $administrador = Administrador::select('nome')->find($pedido->idAdministrador);
            $pedido->administrador = $administrador->nome;
        }
        return [$pedido, $distribuidores];
        //
    }

    // Address/Customer Methods
    function ajustarCoordenadas(Request $request, $idEndereco)
    {
        $enderecoCliente = EnderecoCliente::find($idEndereco);
        $u = Administrador::find(auth()->user()->id);
        if ($u->tipoAdministrador != "Entregador") {
            $distribuidor = Distribuidor::with("enderecoDistribuidor")
                ->where("id", $request->idDistribuidor ? $request->idDistribuidor : $u->idDistribuidor)
                ->get();

            $enderecoCliente->latitude = $distribuidor[0]->enderecoDistribuidor->latitude;
            $enderecoCliente->longitude = $distribuidor[0]->enderecoDistribuidor->longitude;

            if ($enderecoCliente->save()) {
                return "Coordenadas ajustadas com sucesso.";
            } else {
                return "Erro ao processar coordenadas.";
            }

        } else {
            return "Erro ao processar coordenadas.";
        }
    }
    function listaClientes()
    {
        if (auth()->check()) {

            // $enderecos = DB::table('enderecoCliente')
            // ->select('id as idEndereco', 'logradouro', 'numero', 'bairro', 'complemento', 'cep', 'cidade', 'estado', 'referencia', 'apelido', 'atual', 'idCliente', 'latitude', 'longitude');

            // $users = DB::table('users')
//         ->joinSub($latestPosts, 'latest_posts', function (JoinClause $join) {
//             $join->on('users.id', '=', 'latest_posts.user_id');
//         })->get();

            // $enderecos = DB::table('enderecoCliente')
            // ->select('id as idEndereco', 'logradouro', 'numero', 'bairro', 'complemento', 'cep', 'cidade', 'estado', 'referencia', 'apelido', 'atual', 'idCliente','idCliente', 'latitude', 'latitude')
            // ->whereColumn('idCliente', 'cliente.id');

            // ->select('id','nome','tipoPessoa','cpf','cnpj','precoAcertado','dddTelefone','telefone','outrosContatos','status','email','rating')

            $clientes = Cliente::with('enderecos')
                ->where('status', 1)
                ->select(['id', 'nome', 'tipoPessoa', 'cpf', 'cnpj', 'precoAcertado', 'dddTelefone', 'telefone', 'outrosContatos', 'status', 'email', 'rating'])
                ->get();
            return $clientes;

            //$clientes = DB::table('cliente')
            //->where('status',1)
            //->get()->toArray();
            //$enderecoCliente = DB::table('enderecoCliente')
            //->where('status',1)
            //->get()->toArray();
            //foreach ($enderecoCliente as $endereco) {
            //    foreach ($clientes as $cliente) {
            //        if ($cliente->id == $endereco->idCliente ) {
            //            if (!property_exists($cliente, 'enderecos')){
            //                $cliente->enderecos = array();
            //            }
            //            array_push($cliente->enderecos, $endereco);
            //            break;
            //        }
            //    }
            //}
            //return $clientes;

            // ->join('enderecoCliente', 'pedido.idEndereco', '=','endereco.id''cliente.id', '=', 'enderecoCliente.idCliente')
            // ->select('cliente.*','enderecoCliente.id as idEndereco', 'logradouro', 'numero', 'bairro', 'complemento', 'cep', 'cidade', 'estado', 'referencia', 'apelido', 'atual', 'idCliente', 'latitude', 'longitude')
            // ->get();

            // ->joinSub($enderecos, 'enderecos',function (JoinClause $join) {
            //     $join->on('cliente.id', '=', 'enderecos.idCliente')
            //     ->where('cliente.status',1);
            // })->get();

            //->where('cliente.status',1);
            //'cliente.id', '=', 'enderecoCliente.idCliente'

            // return Datatables::of($clientes)
            //     ->editColumn('nome', function(Cliente $cliente) {
            //         if($cliente->precoAcertado == 1){
            //             return '<i class="far fa-handshake"></i> '.$cliente->nome;
            //         }else{
            //             return $cliente->nome;
            //         }
            //     })
            //     ->editColumn('tipoPessoa', function(Cliente $cliente) {
            //         if($cliente->tipoPessoa == 1){
            //             return $cliente->cpf;
            //         }else{
            //             return $cliente->cnpj;
            //         }
            //     })
            //     ->editColumn('telefone', '({{$dddTelefone}}) {{$telefone}}')
            //     ->addColumn('rating', function(Cliente $cliente) {
            //         if($cliente->rating > 0){
            //             return '<span class="badge badge-success">'.$cliente->rating.'</span>';
            //         }else if($cliente->rating == 0){
            //             return '<span class="badge badge-secondary">'.$cliente->rating.'</span>';
            //         }else if($cliente->rating < -2){
            //             return '<span class="badge badge-danger">'.$cliente->rating.'</span>';
            //         }else{
            //             return '<span class="badge badge-warning">'.$cliente->rating.'</span>';
            //         }
            //     })
            //     //->addColumn('endereco', '<button title="Endereços" id='.'{{$id}}'.' type="button" class="btn btn-circle btn-info"><i class="mdi mdi-map-marker-radius"></i></button>')
            //     ->addColumn('endereco', function(Cliente $cliente) {
            //         return '<button title="Endereços" id="'.$cliente->id.'" name="'.$cliente->nome.'" value="'.$cliente->precoAcertado.'" type="button" class="btn btn-circle btn-info"><i class="mdi mdi-map-marker-radius"></i></button>';
            //     })
            //     ->rawColumns(['rating','endereco','nome'])
            //     ->make(true);

        } else {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }
    }
}
