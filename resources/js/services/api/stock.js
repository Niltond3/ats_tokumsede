export const listAllStock = async () => {
    return await axios.get("/relatorio/estoque");
};

export const getStockByDistributor = async () => {
    return await axios.get("/estoque");
};

export const updateStock = async (stockId, quantity) => {
    return await axios.put(`/estoque/${stockId}`, { quantidade: quantity });
};

export const getStockReport = async (distributorIds) => {
    return await axios.post("/relatorio/estoque", {
        idDistribuidores: distributorIds
    });
};
