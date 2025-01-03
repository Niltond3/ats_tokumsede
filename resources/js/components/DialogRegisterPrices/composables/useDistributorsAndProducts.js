import { ref } from "vue";
import {
    listAllDistributors,
    getDistributorForClientAddress,
} from "@/services/api/distributors";
import { listProductsByDistributor } from "@/services/api/products";
import { utf8Decode } from "@/util";
import renderToast from "@/components/renderPromiseToast";

export function useDistributorsAndProducts() {
    const tableIdentifier = ref(null);
    const distributor = ref(null);
    const distributors = ref([]);
    const loadingDistributors = ref(true);
    const loadingProducts = ref(true);

    const fetchDistributor = (addressId) => {
        const promise = getDistributorForClientAddress(addressId);
        const response = renderToast(
            promise,
            "carregando distribuidor ...",
            "Distribuidor carregado",
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
            "error sei lá",
            (err) => {
                console.log(err);
            }
        );
        return response;
    };

    const fetchDistributors = () => {
        const promise = listAllDistributors();
        renderToast(
            promise,
            "carregando distribuidores ...",
            "Distribuidores carregados",
            (response) => {
                distributors.value = response.data.data.map((distributor) => ({
                    ...distributor,
                    nome: utf8Decode(distributor.nome),
                }));
                loadingDistributors.value = false;
            }
        );
    };

    const fetchProductsForDistributor = async (distributorId) => {
        loadingProducts.value = true;
        const promise = listProductsByDistributor(distributorId);

        const response = renderToast(
            promise,
            "Carregando produtos...",
            "Produtos carregados com sucesso",
            (response) => response.data,
            "Falha ao carregar produtos",
            () => []
        );
        response.finally(() => (loadingProducts.value = false));
        return response;
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
