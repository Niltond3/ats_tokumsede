export const createStockUnion = (data) => {
    return axios.post('/stock-unions', data);
};
