export const updateProductPrices = async (productData) => {
    return await axios.put("preco/update", productData);
};
