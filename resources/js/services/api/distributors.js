export const listAllDistributors = async () => {
    return await axios.get("distribuidores/todosOsDistribuidores");
};

export const getDistributorForAddress = async (addressId) => {
    return await axios.get(`distribuidores/listarPorEndereco/${addressId}`);
};

export const getDistributorForClientAddress = async (addressId) => {
    return await axios.get(
        `distribuidores/listarPorEnderecoCliente/${addressId}`
    );
};
