/**
 * Client API service module
 * Provides methods for interacting with the client endpoints
 */

/**
 * Fetch all clients from the system
 * @returns {Promise} Resolves with list of all clients
 */
export const getClients = () => axios.get('/clientes');

/**
 * Fetch a single client by their ID
 * @param {number} clientId - The ID of the client to fetch
 * @returns {Promise} Resolves with the client data
 */
export const getClient = async (clientId) => axios.get(`/clientes/${clientId}`);

/**
 * Create a new client in the system
 * @param {Object} clientData - The client data to create
 * @returns {Promise} Resolves with the created client
 */
export const createClient = (clientData) => axios.post('/clientes', clientData);

/**
 * Update an existing client's information
 * @param {number} clientId - The ID of the client to update
 * @param {Object} clientData - The updated client data
 * @returns {Promise} Resolves with the updated client
 */
export const updateClient = (clientId, clientData) => axios.put(`clientes/${clientId}`, clientData);

/**
 * Delete a client from the system
 * @param {number} clientId - The ID of the client to delete
 * @returns {Promise} Resolves when client is deleted
 */
export const deleteClient = (clientId) => axios.delete(`clientes/${clientId}`);

/**
 * Get coordinates for a client address
 * @param {Object} address - The address to geocode
 * @returns {Promise} Resolves with the coordinates
 */
export const getClientCoordinates = (address) => axios.get('clientes/buscar-latitude-longitude', { params: address });

/**
 * Update a client's password
 * @param {number} clientId - The ID of the client
 * @param {Object} passwordData - Contains old_password and new_password
 * @returns {Promise} Resolves when password is updated
 */
export const updatePassword = (clientId, passwordData) => axios.put(`clientes/${clientId}/password`, passwordData);
/**
 * Delete the authenticated client's profile
 * @param {Object} data - Contains password for verification
 * @returns {Promise} Resolves when profile is deleted
 */
export const deleteProfile = (data) =>
    axios.delete('cliente/profile', { data });
