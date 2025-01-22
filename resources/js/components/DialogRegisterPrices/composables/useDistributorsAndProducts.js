import { ref } from "vue";
import {
    listAllDistributors,
    getDistributorForClientAddress,
    getDistributor,
} from "@/services/api/distributors";
import {
    listProductsByDistributor,
} from "@/services/api/products";
import { utf8Decode } from "@/util";
import renderToast from "@/components/renderPromiseToast";

export function useDistributorsAndProducts() {
    const tableIdentifier = ref(null);
    const distributor = ref(null);
    const distributors = ref([]);
    const loadingDistributors = ref(true);
    const loadingProducts = ref(true);

    const fetchDistributor = (addressId, distributorId) => {
        const promise = distributorId
            ? getDistributor(distributorId)
            : getDistributorForClientAddress(addressId);

        const response = renderToast(
            promise,
            "carregando distribuidor ...",
            "Distribuidor carregado",
            "erro ao carregar distribuidor",
            (response) => {
                const formatResponse = {
                    ...response.data.data,
                    nome: utf8Decode(response.data.data.nome),
                };
                distributor.value = formatResponse;
                tableIdentifier.value = formatResponse.nome;
                loadingDistributors.value = false;
                return formatResponse;
            },
            (err) => console.error(err)
        );

        return response;
    };

    const fetchDistributors = () => {
        const promise = listAllDistributors();
        renderToast(
            promise,
            "carregando distribuidores ...",
            "Distribuidores carregados",
            "erro ao carregar distribuidores",
            (response) => {
                distributors.value = response.data.data.map((distributor) => ({
                    ...distributor,
                    nome: utf8Decode(distributor.nome),
                }));
                loadingDistributors.value = false;
            }
        );
    };

    const fetchProducts = async (promise) => {
        const response = renderToast(
            promise,
            "Carregando produtos...",
            "Produtos carregados com sucesso",
            "Falha ao carregar produtos",
            (response) => response.data,
            () => []
        );
        response.finally(() => (loadingProducts.value = false));
        return response;
    };

    const fetchProductsForDistributor = async (distributorId, clientId) => {
        loadingProducts.value = true;
        const promise = listProductsByDistributor(distributorId, clientId);
        return fetchProducts(promise);
    };

    return {
        tableIdentifier,
        distributor,
        distributors,
        loadingDistributors,
        loadingProducts,
        fetchDistributor,
        fetchDistributors,
        fetchProductsForDistributor,
    };
}
