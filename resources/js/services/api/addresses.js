import axios from 'axios';

/**
 * Get all addresses for a client
 * @param {number} clientId - Client ID
 * @returns {Promise} List of addresses
 */
export const getAddresses = (clientId) =>
    axios.get(`enderecos/${clientId}`);

/**
 * Create new address
 * @param {Object} addressData - Address information
 * @returns {Promise} Created address
 */
export const createAddress = (addressData) =>
    axios.post('enderecos', addressData);

/**
 * Update existing address
 * @param {number} addressId - Address ID
 * @param {Object} addressData - Updated address data
 * @returns {Promise} Updated address
 */
export const updateAddress = (addressId, addressData) =>
    axios.put(`enderecos/${addressId}`, addressData);

/**
 * Update address coordinates
 * @param {number} addressId - Address ID
 * @returns {Promise} Updated coordinates
 */
export const updateCoordinates = (addressId) =>
    axios.put(`enderecos/${addressId}/coordinates`);
