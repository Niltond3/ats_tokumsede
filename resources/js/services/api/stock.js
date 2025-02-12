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
export const unifyStock = async (mainId, secondaryIds) => {
    return await axios.post('/stock-unions', {
        main_distributor_id: mainId,
        secondary_distributor_ids: secondaryIds,
    })
}
