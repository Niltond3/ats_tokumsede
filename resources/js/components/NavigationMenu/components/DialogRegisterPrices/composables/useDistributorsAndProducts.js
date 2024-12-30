import { ref } from 'vue'
import { listAllDistributors } from '@/services/api/distributors'
import { listProductsByDistributor } from '@/services/api/products'
import { utf8Decode } from '@/util'
import renderToast from '@/components/renderPromiseToast'

export function useDistributorsAndProducts() {
    const distributors = ref([])
    const loadingDistributors = ref(true)
    const loadingProducts = ref(true)

    const fetchDistributors = () => {
        const promise = listAllDistributors()
        renderToast(
            promise,
            'carregando distribuidores ...',
            'Distribuidores carregados',
            (response) => {
                distributors.value = response.data.data.map((distributor) => ({
                    ...distributor,
                    nome: utf8Decode(distributor.nome)
                }))
                loadingDistributors.value = false
            }
        )
    }

    const fetchProductsForDistributor = async (distributorId) => {
        loadingProducts.value = true
        const promise = listProductsByDistributor(distributorId)

        const response = renderToast(
            promise,
            'Carregando produtos...',
            'Produtos carregados com sucesso',
            (response) => response.data,
            'Falha ao carregar produtos',
            () => []
        )
        response.finally(() => loadingProducts.value = false)
        return response
    }

    return {
        distributors,
        loadingDistributors,
        loadingProducts,
        fetchDistributors,
        fetchProductsForDistributor
    }
}
