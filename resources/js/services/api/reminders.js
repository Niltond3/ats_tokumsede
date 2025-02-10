import axios from 'axios';

/**
 * Get all reminders
 * @returns {Promise} List of reminders
 */
export const getReminders = () =>
    axios.get('/reminders');

/**
 * Create reminder
 * @param {Object} reminderData - Reminder information
 * @returns {Promise} Created reminder
 */
export const createReminder = (reminderData) =>
    axios.post('/reminders', reminderData);

/**
 * Update reminder
 * @param {number} reminderId - Reminder ID
 * @param {Object} reminderData - Updated reminder data
 * @returns {Promise} Updated reminder
 */
export const updateReminder = (reminderId, reminderData) =>
    axios.put(`/reminders/${reminderId}`, reminderData);

/**
 * Delete reminder
 * @param {number} reminderId - Reminder ID
 * @returns {Promise} Deletion confirmation
 */
export const deleteReminder = (reminderId) =>
    axios.delete(`/reminders/${reminderId}`);
