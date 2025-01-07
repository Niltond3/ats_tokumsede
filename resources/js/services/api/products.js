export const saveProductPrice = async (productData) => {
    console.log(productData)
    return await axios.post("/preco/", productData);
};

export const updateProductPrices = async (productData) => {
    console.log(productData)
    return await axios.put("/preco/", productData);
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
