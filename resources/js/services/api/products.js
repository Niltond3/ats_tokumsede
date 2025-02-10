export const getAllPrices = async () => {
    return await axios.get("/preco");
};

export const saveProductPrice = async (productData) => {
    return await axios.post("/preco", productData);
};

export const getProductPrice = async (productId) => {
    return await axios.get(`/preco/${productId}`);
};

export const updateProductPrices = async (productData) => {
    return await axios.put("/preco", productData);
};

export const updateProductStatus = async (productId, newStatus) => {
    return await axios.put(
        `/produtos/status/${productId}/${newStatus}`
    );
}

export const deleteProductPrice = async (priceId) => {
    return await axios.delete(`/preco/${priceId}`);
};

export const listProductsByDistributor = async (distributorId, clientId) => {
    return await axios.get(
        `produtos/listarPorDistribuidor/${distributorId}${clientId ? `/${clientId}` : ""
        }`
    );
};

export const listProductsByClient = async (idDistribuidor, idCliente) => {
    return await axios.get(
        `produtos/listarProdutos/${idDistribuidor}/${idCliente}`
    );
};

/**
 * Create product
 * @param {Object} productData - Product information
 * @returns {Promise} Created product
 */
export const createProduct = (productData) =>
    axios.post('/produtos', productData);

/**
 * Update product
 * @param {number} productId - Product ID
 * @param {Object} productData - Updated product data
 * @returns {Promise} Updated product
 */
export const updateProduct = (productId, productData) =>
    axios.put(`/produtos/${productId}`, productData);

/**
 * Delete product
 * @param {number} productId - Product ID
 * @returns {Promise} Deletion confirmation
 */
export const deleteProduct = (productId) =>
    axios.delete(`/produtos/${productId}`);
