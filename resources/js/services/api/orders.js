import axios from "axios";

/**
 * Fetches all orders
 * @returns {Promise} List of orders
 */
export const getOrder = async () => {
    return await axios.get("pedidos");
};

/**
 * Get order details by ID
 * @param {number} orderId - Order ID
 * @returns {Promise} Order details
 */
export const getOrderById = async (orderId) => {
    return await axios.get(`pedidos/${orderId}`);
};

/**
 * Create new order
 * @param {Object} orderData - Order data
 * @returns {Promise} Created order
 */
export const createOrder = async (orderData) => {
    return await axios.post("pedidos", orderData);
};

/**
 * Update existing order
 * @param {number} orderId - Order ID
 * @param {Object} orderData - Updated order data
 * @returns {Promise} Updated order
 */
export const updateOrder = async (orderId, orderData) => {
    return await axios.put(`pedidos/${orderId}`, orderData);
};

/**
 * Delete order
 * @param {number} orderId - Order ID
 * @returns {Promise} Delete confirmation
 */
export const deleteOrder = async (orderId) => {
    return await axios.delete(`pedidos/${orderId}`);
};

/**
 * View order details
 * @param {number} orderId - Order ID
 * @returns {Promise} Detailed order view
 */
export const viewOrder = async (orderId) => {
    return await axios.get(`pedidos/visualizar/${orderId}`);
};

/**
 * Set order status to pending
 * @param {number} orderId - Order ID
 * @returns {Promise} Updated order status
 */
export const setPendingOrder = async (orderId) => {
    return await axios.put(`pedidos/setPendente/${orderId}`);
};

/**
 * Accept order
 * @param {number} orderId - Order ID
 * @returns {Promise} Updated order status
 */
export const orderAccept = (orderId) => {
    return axios.put(`pedidos/aceitar/${orderId}`);
};

/**
 * Dispatch order with delivery person
 * @param {number} orderId - Order ID
 * @param {Object} deliveryMan - Delivery person details
 * @returns {Promise} Updated order status
 */
export const orderDispatch = (orderId, deliveryMan) => {
    return axios.put(`pedidos/despachar/${orderId}`, {
        entregador: deliveryMan,
    });
};

/**
 * Mark order as delivered
 * @param {number} orderId - Order ID
 * @returns {Promise} Updated order status
 */
export const orderDeliver = (orderId) => {
    return axios.put(`pedidos/entregar/${orderId}`);
};

/**
 * Reject order with reason
 * @param {number} orderId - Order ID
 * @param {string} reason - Rejection reason
 * @returns {Promise} Updated order status
 */
export const orderReject = (orderId, reason) => {
    return axios.put(`pedidos/recusar/${orderId}`, { retorno: reason });
};

/**
 * Cancel order
 * @param {number} orderId - Order ID
 * @returns {Promise} Updated order status
 */
export const cancelOrder = async (orderId) => {
    return await axios.put(`pedidos/cancelar/${orderId}`);
};

/**
 * Edit order
 * @param {number} orderId - Order ID
 * @returns {Promise} Order edit form data
 */
export const editOrder = async (orderId) => {
    return await axios.get(`pedidos/editar/${orderId}`);
};

/**
 * Update order details
 * @param {number} orderId - Order ID
 * @param {Object} orderData - Updated order data
 * @returns {Promise} Updated order
 */
export const updateOrderDetails = async (orderId, orderData) => {
    return await axios.put(`pedidos/atualizar/${orderId}`, orderData);
};

/**
 * Choose delivery person for order
 * @param {number} orderId - Order ID
 * @returns {Promise} Available delivery persons
 */
export const chooseDeliveryPerson = async (orderId) => {
    return await axios.get(`pedidos/escolherentregador/${orderId}`);
};

/**
 * Adjust order coordinates
 * @param {number} orderId - Order ID
 * @param {Object} coordinates - Updated coordinates
 * @returns {Promise} Updated order coordinates
 */
export const adjustCoordinates = async (orderId, coordinates) => {
    return await axios.post(`pedidos/ajustarCoordenadas/${orderId}`, coordinates);
};

/**
 * Get new orders
 * @param {number} id - Reference ID
 * @returns {Promise} List of new orders
 */
export const getNewOrders = async (id) => {
    return await axios.get(`pedidos/buscarNovosPedidos/${id}`);
};

/**
 * Get last order
 * @returns {Promise} Last order details
 */
export const getLastOrder = async () => {
    return await axios.get("pedidos/ultimoPedido");
};

/**
 * Get customer list
 * @returns {Promise} List of customers
 */
export const getCustomerList = async () => {
    return await axios.get("pedidos/listaClientes");
};
/**
 * Get order report
 * @param {Object} filters - Report filters
 * @returns {Promise} Report data
 */
export const getOrderReport = (filters) =>
    axios.post('/relatorio/pedidos', filters);

/**
 * Get sales report by product
 * @param {Object} filters - Report filters
 * @returns {Promise} Product sales report
 */
export const getProductSalesReport = (filters) =>
    axios.post('pedidos/relatorio/vendas/produto', filters);

/**
 * Get sales report by delivery person
 * @param {Object} filters - Report filters
 * @returns {Promise} Delivery person sales report
 */
export const getDeliveryPersonSalesReport = (filters) =>
    axios.post('pedidos/relatorio/vendas/entregador', filters);

/**
 * Get sales report
 * @param {Object} filters - Report filters
 * @returns {Promise} Sales report data
 */
export const getSalesReport = (filters) =>
    axios.post('/relatorio/vendas', filters);
