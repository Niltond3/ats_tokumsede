import { ref, computed, onMounted } from 'vue';
import { listProducts } from '@/services/api/products';
import renderToast from '@/components/renderPromiseToast';
import { StringUtil } from '@/util';

/**
 * Hook para gerenciar a seleção de produtos.
 * @returns {Object} produtos, produtosSelecionados, termoBusca, aberto, produtosFiltrados, addProduct, removeProduct, loadProducts
 */
export function useProductSelector() {
    const products = ref([]);
    const selectedProducts = ref([]);
    const searchTerm = ref('');
    const open = ref(false);

    const loadProducts = () => {
        renderToast(
            listProducts(),
            'Carregando produtos...',
            'Produtos carregados',
            'Erro ao carregar produtos',
            (response) => {
                const recomposeProducts = response.data
                    .map((product) => {
                        const nome = StringUtil.utf8Decode(product.nome);
                        return { ...product, nome };
                    })
                    .filter((product) => product.componente == 1 && product.composicao == 0);
                products.value = recomposeProducts;
            },
        );
    };

    onMounted(loadProducts);

    const filteredProducts = computed(() =>
        products.value.filter(
            (product) =>
                !selectedProducts.value.some((item) => item.id === product.id) &&
                product.nome.toLowerCase().includes(searchTerm.value.toLowerCase())
        )
    );

    const setSelectedProducts = (products) => {
        selectedProducts.value = !products?.length ? [] : products.map(product => ({
            id: product.id,
            nome: StringUtil.utf8Decode(product.nome)
        }));
    };

    const addProduct = (product) => {
        if (!selectedProducts.value.some((item) => item.id === product.id)) {
            selectedProducts.value.push({ id: product.id, nome: product.nome });
        }
    };

    const removeProduct = (id) => {
        selectedProducts.value = selectedProducts.value.filter((item) => item.id !== id);
    };

    return {
        products,
        selectedProducts,
        searchTerm,
        open,
        filteredProducts,
        addProduct,
        removeProduct,
        loadProducts,
        setSelectedProducts,
    };
}
