<script setup>
import renderToast from '@/components/renderPromiseToast';
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { updateProductPrices } from 'services/api/products';

const props = defineProps({
    loadingProducts: Boolean,
    distributorId: Number,
    products: Array
})
/*
 'idProduto' => 'required|exists:produto,id',
            'idDistribuidor' => 'required|exists:distribuidor,id',
            'valor' => 'required|numeric|min:0',
            'qtdMin' => 'required|integer|min:1',
            'id' => 'nullable|exists:preco,id'
*/
const handleUpdate = async () => {
    try {
        const updatedProducts = props.products.filter(product => product.updated === true)

        // Using Promise.all for parallel requests
        const updatePromises = updatedProducts.map(product => {
            return updateProductPrices({
                idProduto: product.id,
                idDistribuidor: distributorId,
                valor: product.preco[product.preco.length - 1].val,
                qtdMin: product.preco[product.preco.length - 1].qtd
            })
        })
        const promisses = Promise.all(updatePromises)
        renderToast(promisses, 'Atualizando preços...', 'Preços atualizados com sucesso', (sucss) => console.log(sucss), 'erro: erro ao atualizar os preços', (err) => console.log(err))

        // Handle success (emit event, show notification, etc)
    } catch (error) {
        console.log(error);
    }
}

</script>
<template>
    <div>
        <template v-if="loadingProducts">
            <Skeleton class="sm:col-span-2 sm:col-end-13 sm:row-span-2 sm:my-3 h-10 w-full" />
        </template>
        <template v-else>
            <Button type="submit"
                class="sm:col-span-2 sm:col-end-13 sm:row-span-2 sm:my-3 border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/60 disabled:hover:bg-info/60 disabled:cursor-not-allowed"
                @click="handleUpdate">
                <span>Atualizar</span>
            </Button>
        </template>
    </div>
</template>
