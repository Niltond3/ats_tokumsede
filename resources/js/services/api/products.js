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
