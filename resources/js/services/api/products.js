export const updateProductPrices = async (productData) => {
    return await axios.put("/preco/", productData);
};

export const listProductsByDistributor = async (distributorId) => {
    return await axios.get(`produtos/listarPorDistribuidor/${distributorId}`);
};

export const listProductsByClientAndDistributor = async (idDistribuidor,
    idCliente) => {
    return await axios.get(`produtos/listarProdutos/${idDistribuidor}/${idCliente}`);
};
