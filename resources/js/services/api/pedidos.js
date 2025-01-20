export const getAllPedidos = async () => {
    return await axios.get("/pedidos");
};

export const savePedido = async (pedidoData) => {
    return await axios.post("/pedidos", pedidoData);
};

export const getPedido = async (pedidoId) => {
    return await axios.get(`/pedidos/${pedidoId}`);
};

export const updatePedido = async (pedidoId, pedidoData) => {
    return await axios.put(`/pedidos/${pedidoId}`, pedidoData);
};

export const deletePedido = async (pedidoId) => {
    return await axios.delete(`/pedidos/${pedidoId}`);
};

export const visualizarPedido = async (pedidoId) => {
    return await axios.get(`pedidos/visualizar/${pedidoId}`);
};

export const setPedidoPendente = async (pedidoId) => {
    return await axios.put(`pedidos/setPendente/${pedidoId}`);
};

export const aceitarPedido = async (pedidoId) => {
    return await axios.put(`pedidos/aceitar/${pedidoId}`);
};

export const despacharPedido = async (pedidoId, idEntregador) => {
    return await axios.put(`pedidos/despachar/${pedidoId}`, { entregador: idEntregador });
};

export const recusarPedido = async (pedidoId, retorno) => {
    return await axios.put(`pedidos/recusar/${pedidoId}`, { retorno: retorno });
};

export const entregarPedido = async (pedidoId) => {
    return await axios.put(`pedidos/entregar/${pedidoId}`);
};

export const cancelarPedido = async (pedidoId) => {
    return await axios.put(`pedidos/cancelar/${pedidoId}`);
};

export const editarPedido = async (pedidoId) => {
    return await axios.get(`pedidos/editar/${pedidoId}`);
};

export const atualizarPedido = async (pedidoId, pedidoData) => {
    return await axios.put(`pedidos/atualizar/${pedidoId}`, pedidoData);
};

export const escolherEntregador = async (pedidoId) => {
    return await axios.get(`pedidos/escolherentregador/${pedidoId}`);
};

export const ajustarCoordenadas = async (pedidoId, coordenadas) => {
    return await axios.post(`pedidos/ajustarCoordenadas/${pedidoId}`, coordenadas);
};

export const buscarNovosPedidos = async (id) => {
    return await axios.get(`pedidos/buscarNovosPedidos/${id}`);
};

export const getUltimoPedido = async () => {
    return await axios.get('pedidos/ultimoPedido');
};

export const getListaClientes = async () => {
    return await axios.get('pedidos/listaClientes');
};
