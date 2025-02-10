import axios from 'axios';

/**
 * Get all categories
 * @returns {Promise} List of categories
 */
export const getCategories = () =>
    axios.get('categorias');

/**
 * Get category by ID
 * @param {number} categoryId - Category ID
 * @returns {Promise} Category details
 */
export const getCategory = (categoryId) =>
    axios.get(`categorias/${categoryId}`);

/**
 * Create new category
 * @param {Object} categoryData - Category information
 * @returns {Promise} Created category
 */
export const createCategory = (categoryData) =>
    axios.post('categorias', categoryData);

/**
 * Update category
 * @param {number} categoryId - Category ID
 * @param {Object} categoryData - Updated category data
 * @returns {Promise} Updated category
 */
export const updateCategory = (categoryId, categoryData) =>
    axios.put(`categorias/${categoryId}`, categoryData);

/**
 * Delete category
 * @param {number} categoryId - Category ID
 * @returns {Promise} Deletion confirmation
 */
export const deleteCategory = (categoryId) =>
    axios.delete(`categorias/${categoryId}`);
