<script setup>
import { ref, markRaw, defineComponent, h, onMounted, watch } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogTrigger,
    DialogHeader,
    DialogTitle,
    DialogDescription
} from '@/components/ui/dialog'
import { DataTableProducts } from '../../DataTableProducts';
import { utf8Decode, formatMoney } from '@/util';
import { formatOrder, orderToClipboard } from '../../utils';

const { toCurrency } = formatMoney()

const props = defineProps({
    orderId: { type: Number, required: true },
});

const createOrderData = ref()

const urlOrder = `pedidos/editar/${props.orderId}`
const responseOrder = await axios.get(urlOrder)

const { data: orderEditData } = responseOrder

const orderData = orderEditData[0]
const distributorsData = orderEditData[1]

const distributors = distributorsData.filter(distributor => distributor.status == 1)

const idDistribuidor = orderData.distribuidor.id
const idCliente = orderData.cliente.id
const urlProducts = `produtos/listarProdutos/${idDistribuidor}/${idCliente}`

const responseProducts = await axios.get(urlProducts)
const { data: productsData } = responseProducts
const distributorExpedient = productsData[2];
const distributorTaxes = productsData[3];

const itensPedido = orderData.itensPedido.map((order) => { return { ...order, preco: toCurrency(order.preco), subtotal: toCurrency(order.subtotal), produto: { ...order.produto, nome: utf8Decode(order.produto.nome) } } })

const products = productsData[0].map(product => {
    const orderItem = itensPedido.find(item => item.produto.id == product.id)
    const quantidade = orderItem ? orderItem.qtd : 0

    return {
        ...product,
        quantidade
    }
})



const formatedOrder = formatOrder(orderData)

const order = {
    ...formatedOrder, itensPedido
}

createOrderData.value = {
    order,
    products: products.value,
    distributor: order.distribuidor,
    address: order.endereco,
    distributorExpedient: distributorExpedient.value,
    distributorTaxes: distributorTaxes.value,
}

const handleCopyOrder = (order) => orderToClipboard(order)

</script>

<template>
    <DialogContent class="text-sm max-w-[22rem] sm:max-w-3xl md:max-w-[40rem]">
        <DialogHeader>
            <DialogTitle class=" font-medium text-info leading-none flex gap-3 justify-between mr-4">
                <button class="group" @click="handleCopyOrder(order)">#{{ createOrderData.order.id }} <i
                        class="ri-file-copy-fill opacity-75 group-hover:opacity-100 transition-all"></i></button>
                <span>{{ createOrderData.distributor.nome }}</span>
            </DialogTitle>
            <DialogDescription>
                Clique no botão "Salvar" para salvar as alterações
            </DialogDescription>
        </DialogHeader>
        <DataTableProducts @callback:payload-pedido="handleUpdateOrder" :create-order-data="createOrderData"
            :distributors="distributors"></DataTableProducts>
    </DialogContent>
</template>
