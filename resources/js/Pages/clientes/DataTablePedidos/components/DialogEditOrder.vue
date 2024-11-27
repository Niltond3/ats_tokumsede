<script setup>
import { ref, markRaw, defineComponent, h, onMounted } from 'vue';
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
import { formatOrder, getStatusString } from '../../utils';
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

const whenDialogOpen = async () => {
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
}

onMounted(() => whenDialogOpen())

function handleCopyOrder() {
    const { id: orderId, total, formaPagamento, troco, status: { label: statusLabel }, horarioPedido, horarioAceito, horarioEntrega, horarioDespache, horarioCancelado, dataAgendada, horaInicio, cliente: { nome: cliente, telefone }, distribuidor: { nome: distribuidorNome }, endereco: { logradouro, numero, bairro, complemento, cidade, estado, referencia }, itensPedido } = data.value

    const date = dateToISOFormat(horarioPedido)

    var minute = date.getMinutes().toString().padStart(2, '0');
    var hour = date.getHours().toString().padStart(2, '0');

    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };

    const formatDate = `${date.toLocaleDateString('pt-BR', options)} as HH:mm: ${hour}:${minute}`;

    const produtos = itensPedido.map((order) => `${order.qtd} ${order.produto.nome} un ${order.preco} subtotal ${order.subtotal}`).join('\n')

    navigator.clipboard.writeText(`
    #: ${orderId} | cliente: ${cliente}, Telefone: ${telefone}
    criado: ${formatDate}
    status: ${statusLabel}
    Endereço: ${logradouro}, nº ${numero} - ${complemento}, ${bairro} - ${cidade} ${estado} - ${referencia}
    distribuidor: ${distribuidorNome}
    ${produtos}`)
    toast.info('Copiado para a área de transferência', { position: 'top-center' })
}

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
            <DropdownMenuItem v-if="dropdown" class="cursor-pointer" @select="(e) => e.preventDefault()">
                <i class="ri-edit-2-fill"></i>
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
                    <button class="group" @click="handleCopyOrder">#{{ createOrderData.order.id }} <i
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
