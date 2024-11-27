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
import { utf8Decode, formatMoney } from '@/util';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { formatOrder, orderToClipboard } from '../../utils';
import { toast } from 'vue-sonner'
import { DataTableProducts } from '../../DataTableProducts';
import { dialogState } from '../../../../hooks/useToggleDialog'


const props = defineProps({
    orderId: { type: Number, required: true },
    dropdown: { type: Boolean, required: false, default: true }
});

const emits = defineEmits(['callback:editOrder', 'update:dialogOpen'])

const data = ref({})
const distributors = ref([])
const createOrderData = ref()

const { isOpen, toggleDialog } = dialogState()
const { toCurrency } = formatMoney()

const fetchOrder = async () => {
    const urlOrder = `pedidos/editar/${props.orderId}`
    const responseOrder = await axios.get(urlOrder)
    const { data: orderEditData } = responseOrder

    const orderData = orderEditData[0]
    const distributorsData = orderEditData[1]

    distributors.value = distributorsData.filter(distributor => distributor.status == 1)

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
        products,
        distributor: order.distribuidor,
        address: order.endereco,
        distributorExpedient,
        distributorTaxes,
    }
    data.value = order
}

watch(() => isOpen.value, () => fetchOrder())

onMounted(async () => {
    if (props.orderId) fetchOrder()
})

const handleCopyOrder = (order) => orderToClipboard(order)

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})

const renderToast = (promise) => {
    toast.promise(promise, {
        loading: 'Aguarde...',

        success: (data) => {
            emit('callback:editOrder', true)
            toggleDialog()
            return markRaw(CustomDiv('sucesso', `O pedido foi atualizado com sucesso!`));
        },
        error: (data) => markRaw(CustomDiv('Error', JSON.stringify(data.response))),
    });
}

const handleUpdateOrder = (payload) => {
    const url = `pedidos/atualizar/${payload.idPedido}`
    const response = axios.put(url, payload)
    renderToast(response)
}

const handleDialogOpen = (op) => {
    !op && emits('update:dialogOpen', false)
    toggleDialog()
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="handleDialogOpen">
        <DialogTrigger as-child>
            <DropdownMenuItem v-if="dropdown" class="cursor-pointer flex gap-2" @select="(e) => e.preventDefault()">
                <i class="ri-edit-2-fill text-info"></i>
                <span class="hidden min-[426px]:block">Editar Pedido</span>
            </DropdownMenuItem>
            <button v-else
                class="h-8 w-8 rounded-full bg-warning/80 focus:bg-warning text-white shadow-sm hover:shadow-md transition-all">
                <i class="ri-edit-2-fill"></i>
                <span class="hidden min-[426px]:block">Editar Pedido</span>
            </button>
        </DialogTrigger>
        <DialogContent class="text-sm max-w-[22rem] sm:max-w-3xl md:max-w-[40rem]">
            <DialogHeader>
                <DialogTitle class=" font-medium text-info leading-none flex gap-3 justify-between mr-4">
                    <button class="group" @click="handleCopyOrder(data)">#{{ createOrderData.order.id }} <i
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

    </Dialog>
</template>
