<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\Pedidos\{
    ListPedidosAction,
    CreatePedidoAction,
    UpdatePedidoAction,
    ShowPedidoAction,
    SetPendentePedidoAction,
    AceitarPedidoAction,
    DespacharPedidoAction,
    EntregarPedidoAction,
    CancelarPedidoAction,
    RecusarPedidoAction,
    RelatorioVendasAction,
    RelatorioPedidosAction,
    RelatorioVendasProdutoAction,
    RelatorioVendasEntregadorAction,
    BuscarNovosPedidosAction,
    AjustarCoordenadasAction,
    ListaClientesAction,
    EditarPedidoAction,
    AtualizarPedidoAction,
    UltimoPedidoAction,
    VisualizarPedidoAction,
};

class PedidoController extends Controller
{
    private $actions;

    public function __construct(
        ListPedidosAction $listPedidos,
        CreatePedidoAction $createPedido,
        UpdatePedidoAction $updatePedido,
        ShowPedidoAction $showPedido,
        SetPendentePedidoAction $setPendente,
        AceitarPedidoAction $aceitar,
        DespacharPedidoAction $despachar,
        EntregarPedidoAction $entregar,
        CancelarPedidoAction $cancelar,
        RelatorioVendasAction $relatorioVendas,
        RelatorioPedidosAction $relatorioPedidos,
        RelatorioVendasProdutoAction $relatorioVendasProduto,
        RelatorioVendasEntregadorAction $relatorioVendasEntregador,
        BuscarNovosPedidosAction $buscarNovosPedidos,
        AjustarCoordenadasAction $ajustarCoordenadas,
        ListaClientesAction $listaClientes,
        EditarPedidoAction $editarPedido,
        AtualizarPedidoAction $atualizarPedido,
        UltimoPedidoAction $ultimoPedido,
        VisualizarPedidoAction $visualizarPedido,
        RecusarPedidoAction $recusarPedido,
    ) {
        $this->actions = compact(
            'listPedidos',
            'createPedido',
            'updatePedido',
            'showPedido',
            'setPendente',
            'aceitar',
            'despachar',
            'entregar',
            'cancelar',
            'relatorioVendas',
            'relatorioPedidos',
            'relatorioVendasProduto',
            'relatorioVendasEntregador',
            'buscarNovosPedidos',
            'ajustarCoordenadas',
            'listaClientes',
            'editarPedido',
            'atualizarPedido',
            'ultimoPedido',
            'visualizarPedido',
            'recusarPedido'
        );
    }

    public function index()
    {
        if (!auth()->check()) {
            return response('Sua sessão expirou. Por favor, refaça seu login.', 400);
        }
        return $this->actions['listPedidos']->execute(auth()->user());
    }

    public function store(Request $request)
    {
        return $this->actions['createPedido']->execute($request);
    }

    public function show($id)
    {
        return $this->actions['showPedido']->execute($id);
    }

    public function update(Request $request, $id)
    {
        return $this->actions['updatePedido']->execute($request, $id);
    }

    public function setPendente($id)
    {
        return $this->actions['setPendente']->execute($id);
    }

    public function aceitar(Request $request, $id)
    {
        return $this->actions['aceitar']->execute($request, $id);
    }

    public function despachar(Request $request, $id)
    {
        return $this->actions['despachar']->execute($request, $id);
    }

    public function entregar($id)
    {
        return $this->actions['entregar']->execute($id);
    }

    public function cancelar(Request $request, $id)
    {
        return $this->actions['cancelar']->execute($request, $id);
    }

    public function recusar(Request $request, $idPedido)
    {
        return $this->actions['recusarPedido']->execute($idPedido, $request);
    }
    public function relatorioVendas(Request $request)
    {
        return $this->actions['relatorioVendas']->execute($request);
    }
    public function relatorioPedidos(Request $request)
    {
    return $this->actions['relatorioPedidos']->execute($request);
    }
    public function relatorioVendasProduto(Request $request)
    {
        return $this->actions['relatorioVendasProduto']->execute($request);
    }

    public function relatorioVendasEntregador(Request $request)
    {
        return $this->actions['relatorioVendasEntregador']->execute($request);
    }

    public function buscarNovosPedidos($ultimoPedidoId)
    {
        return $this->actions['buscarNovosPedidos']->execute($ultimoPedidoId);
    }

    public function ajustarCoordenadas(Request $request, $idEndereco)
    {
        return $this->actions['ajustarCoordenadas']->execute($request, $idEndereco);
    }

    public function listaClientes()
    {
        return $this->actions['listaClientes']->execute();
    }
    public function editar($id)
    {
        return $this->actions['editarPedido']->execute($id);
    }

    public function atualizar(Request $request, $id)
    {
        return $this->actions['atualizarPedido']->execute($request, $id);
    }
    public function ultimoPedido()
    {
    return $this->actions['ultimoPedido']->execute();
    }
    public function visualizar($id)
    {
    return $this->actions['visualizarPedido']->execute($id);
    }
}
