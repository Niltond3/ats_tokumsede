<script setup>
import { ref } from 'vue'
import renderToast from '@/components/renderPromiseToast';
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { updateProductPrices, saveProductPrice } from '@/services/api/products';

const props = defineProps({
    clientId: { type: [String, Number], required: false, default: null },
    loadingProducts: Boolean,
    distributorId: String,
    products: Array
})

const disabledButton = ref(false)

const emits = defineEmits(['sucessUpdate'])

const handleUpdate = async () => {
    disabledButton.value = true
    try {
        const updatedProducts = props.products.filter(product => product.updated === true)
        // Using Promise.all for parallel requests
        const updatePromises = updatedProducts.map(product => {
            const requestData = {
                idProduto: product.id,
                idDistribuidor: props.distributorId,
                qtdMin: product.preco[product.preco.length - 1].qtd
            }
            console.log(requestData)
            console.log(product)
            console.log('clientId: ' + props.clientId)
            console.log('idPreco: ' + product.preco[product.preco.length - 1].precoId)
            console.log('precoEspecial: ' + product.precoEspecial)
            console.log('preco: ' + product.preco[product.preco.length - 1].val)

            return !props.clientId
                ? updateProductPrices({
                    ...requestData,
                    id: product.preco[product.preco.length - 1].precoId,
                    valor: product.preco[product.preco.length - 1].val
                })
                : saveProductPrice({
                    ...requestData,
                    idCliente: props.clientId,
                    valor: product.precoEspecial
                        ? product.precoEspecial[product.precoEspecial.length - 1].val
                        : product.preco[product.preco.length - 1].val
                })
        })
        const promisses = Promise.all(updatePromises)
        renderToast(promisses, 'Atualizando preços...', 'Preços atualizados com sucesso', (res) => {
            emits('sucessUpdate', props.distributorId, props.clientId)
            disabledButton.value = false
            console.log(res)
        }, 'erro: erro ao atualizar os preços', (err) => console.log(err))
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
            <Button type="submit" :disabled="disabledButton"
                class="sm:col-span-3 sm:col-end-13 sm:my-3 border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/60 disabled:hover:bg-info/60 disabled:cursor-not-allowed"
                @click="handleUpdate">
                <span>Atualizar</span>
            </Button>
        </template>
    </div>
</template>
