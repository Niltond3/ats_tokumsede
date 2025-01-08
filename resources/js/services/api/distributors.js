export const listAllDistributors = async () => {
    return await axios.get("/distribuidores/all");
};

export const getDistributor = async (distributorId) => {
    return await axios.get(`/distribuidores/${distributorId}`);
};

export const getDistributorForAddress = async (addressId) => {
    return await axios.get(`/distribuidores/by-address/${addressId}`);
};

export const getDistributorForClientAddress = async (addressId) => {
    return await axios.get(`/distribuidores/by-client-address/${addressId}`);
};
