<script setup>
import renderToast from '@/components/renderPromiseToast';
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { updateProductPrices } from '@/services/api/products';

const props = defineProps({
    clientId: { type: [String, Number], required: false, default: null },
    loadingProducts: Boolean,
    distributorId: String,
    products: Array
})

const emits = defineEmits(['sucessUpdate'])

const handleUpdate = async () => {

    try {
        const updatedProducts = props.products.filter(product => product.updated === true)
        console.log(updatedProducts)
        // Using Promise.all for parallel requests
        const updatePromises = updatedProducts.map(product => {
            return updateProductPrices({
                id: product.idPreco,
                idProduto: product.id,
                idDistribuidor: props.distributorId,
                valor: product.preco[product.preco.length - 1].val,
                qtdMin: product.preco[product.preco.length - 1].qtd
            })
        })
        const promisses = Promise.all(updatePromises)
        renderToast(promisses, 'Atualizando preços...', 'Preços atualizados com sucesso', () => emits('sucessUpdate', props.distributorId), 'erro: erro ao atualizar os preços', (err) => console.log(err))

        // Handle success (emit event, show notification, etc)
    } catch (error) {
        console.log(error);
    }
}

</script>
<template>
    <div class="sm:grid sm:grid-cols-12 sm:gap-4">
        <template v-if="loadingProducts">
            <Skeleton class="sm:col-span-3 sm:col-end-13 sm:my-3 h-10 w-full" />
        </template>
        <template v-else>
            <Button type="submit"
                class="sm:col-span-3 sm:col-end-13 sm:my-3 border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/60 disabled:hover:bg-info/60 disabled:cursor-not-allowed"
                @click="handleUpdate">
                <span>Atualizar</span>
            </Button>
        </template>
    </div>
</template>
