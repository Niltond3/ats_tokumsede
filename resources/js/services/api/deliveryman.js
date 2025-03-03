// src/services/api/deliveryman.js
import axios from 'axios';

export const getDeliverymen = async () => {
    const response = await axios.get('/entregadores');
    console.log(response)
    return response.data;
};

export const createDeliveryman = async (data) => {
    const response = await axios.post('/entregadores', data);
    return response.data;
};

export const updateDeliveryman = async (id, data) => {
    const response = await axios.put(`/entregadores/${id}`, data);
    return response.data;
};

export const deleteDeliverymanAPI = async (id) => {
    const response = await axios.delete(`/entregadores/${id}`);
    return response.data;
};
