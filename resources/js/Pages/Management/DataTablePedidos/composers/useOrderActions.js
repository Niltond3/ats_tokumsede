import renderToast from '@/components/renderPromiseToast'
import {
    aceitarPedido,
    despacharPedido,
    entregarPedido,
    recusarPedido,
    setPedidoPendente
} from '@/services/api/pedidos'

export function useOrderActions(idPedido, loadTable) {

    const handleAceitar = (sucessCalback = null, errorCallback = null) => {
        renderToast(
            aceitarPedido(idPedido),
            'Atualizando Status ...',
            'Pedido aceito com sucesso',
            'Erro ao aceitar pedido',
            sucessCalback,
            errorCallback
        ).finally(() => loadTable())
    }

    const handleDespachar = (deliveryMan, sucessCalback = null, errorCallback = null) => {
        renderToast(
            despacharPedido(idPedido, deliveryMan),
            'Atualizando Status ...',
            'Pedido despachado com sucesso',
            'Erro ao despachar pedido',
            sucessCalback,
            errorCallback
        ).finally(() => loadTable())
    }

    const handleEntregar = (sucessCalback = null, errorCallback = null) => {
        renderToast(
            entregarPedido(idPedido),
            'Atualizando Status ...',
            'Pedido entregue com sucesso',
            'Erro ao entregar pedido',
            sucessCalback,
            errorCallback
        ).finally(() => loadTable())
    }

    const handleCancelar = (reason, sucessCalback = null, errorCallback = null) => {
        renderToast(
            recusarPedido(idPedido, { retorno: reason }),
            'Atualizando Status ...',
            'Pedido cancelado com sucesso',
            'Erro ao cancelar pedido',
            sucessCalback,
            errorCallback
        ).finally(() => loadTable())
    }

    const handleToPendent = (sucessCalback = null, errorCallback = null) => {
        renderToast(
            setPedidoPendente(idPedido),
            'Atualizando Status ...',
            'Pedido marcado como pendente',
            'Erro ao marcar pedido como pendente',
            sucessCalback,
            errorCallback
        ).finally(() => loadTable())
    }

    return {
        handleAceitar,
        handleDespachar,
        handleEntregar,
        handleCancelar,
        handleToPendent
    }
}
