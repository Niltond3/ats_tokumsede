import axios from 'axios';

/**
 * Client Authentication Service
 * Handles all authentication related API calls
 */

/**
 * Authenticate client with credentials
 * @param {Object} credentials - Contains ddd, telefone, senha, remember
 * @returns {Promise} Resolves with login response
 */
export const login = (credentials) =>
    axios.post('/cliente/login', credentials, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    });

/**
 * Log out the current client
 * @returns {Promise} Resolves when logout is complete
 */
export const logout = () =>
    axios.post('/cliente/logout');

/**
 * Register a new client
 * @param {Object} clientData - Registration data
 * @returns {Promise} Resolves with registration response
 */
export const register = (clientData) =>
    axios.post('/cliente/register', clientData);

/**
 * Get current authenticated client session
 * @returns {Promise} Resolves with session data
 */
export const getSession = () =>
    axios.get('/cliente/session');
