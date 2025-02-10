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
/**
 * Create distributor
 * @param {Object} distributorData - Distributor information
 * @returns {Promise} Created distributor
 */
export const createDistributor = (distributorData) =>
    axios.post('/distribuidores', distributorData);

/**
 * Update distributor
 * @param {number} distributorId - Distributor ID
 * @param {Object} distributorData - Updated distributor data
 * @returns {Promise} Updated distributor
 */
export const updateDistributor = (distributorId, distributorData) =>
    axios.put(`/distribuidores/${distributorId}`, distributorData);

/**
 * Delete distributor
 * @param {number} distributorId - Distributor ID
 * @returns {Promise} Deletion confirmation
 */
export const deleteDistributor = (distributorId) =>
    axios.delete(`/distribuidores/${distributorId}`);
