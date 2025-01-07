import axios from 'axios'

export const orderAccept = async (orderId) => {
    return await axios.post(`pedidos/aceitarPedido/${orderId}`);
};


export const orderDispatch = (orderId, deliveryMan) => {
    return axios.put(`pedidos/despachar/${orderId}`, { entregador: deliveryMan })
}

export const orderDeliver = (orderId) => {
    return axios.put(`pedidos/entregar/${orderId}`)
}

export const orderReject = (orderId, reason) => {
    return axios.put(`pedidos/recusar/${orderId}`, { retorno: reason })
}
