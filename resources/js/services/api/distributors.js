export const listAllDistributors = async () => {
    return await axios.get("distribuidores/todosOsDistribuidores");
};
