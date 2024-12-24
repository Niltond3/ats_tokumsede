<script setup>
// Vue Core
import { ref, onMounted, reactive, watch, computed } from 'vue'
import { useWindowSize } from '@vueuse/core'

// Table Logic
import { useVueTable } from '@tanstack/vue-table'

//composable
import { useOrderState } from '@/composables/orderState'
import { useResponsiveColumns } from './composable/useResponsiveColumns'
import { useEventHandlers } from './composable/useEventHandlers'

// Configuration
import { columns } from './config/Columns'
import { createTableOptions } from './config/tableConfig'

// Utilities
import { toast } from 'vue-sonner'
import { formatMoney } from '@/util'
import renderToast from '@/components/renderPromiseToast'
import useDataToTableFormat from './composable/dataToTableFormat'
import DataTableProducts from './components/DataTableProducts'

const props = defineProps({
    createOrderData: { type: null, required: false },
    distributors: { type: Array, required: false },
})

const orderState = useOrderState()

const clientId = ref(null)

const isUpdate = ref(false)

const pageSizes = ref([50])

const sorting = ref([])

const globalFilter = ref('')

const tableIdentifier = ref('')

const addressNote = ref('')

const disabledButton = ref(true)

const resizebleColumns = ref(columns)

const { toFloat } = formatMoney()

const { width } = useWindowSize()

const emit = defineEmits(['callback:payloadPedido', 'update:specialOfferCreated'])

const createSpecialOffer = (payload) => {
    const url = 'preco'
    const promise = axios.post(url, payload)
    renderToast(promise, 'Salvando oferta ...', 'oferta salva com sucesso!', () => {
        emit('update:specialOfferCreated', true)
    }, 'Erro ao Salvar oferta!', (err) => {
        console.log(err)
    })
}

const { handleDistributor, handleCallbackPedido, handleExchange, handlePayForm, handleScheduling, handleUpdateOrderNote, handleUpdateStatus } = useEventHandlers(orderState, emit, addressNote, disabledButton)

const computedOrderTotals = computed(() => {
    const itens = orderState.tableData
        .filter(product => product.quantidade > 0)
        .map(product => ({
            idProduto: product.id,
            quantidade: product.quantidade,
            preco: product.precoEspecial
                ? product.precoEspecial[product.precoEspecial.length - 1].val
                : product.preco[product.preco.length - 1].val,
            subtotal: product.quantidade * (product.precoEspecial
                ? product.precoEspecial[product.precoEspecial.length - 1].val
                : product.preco[product.preco.length - 1].val),
            precoAcertado: null
        }))

    return {
        itens,
        totalProdutos: itens.reduce((sum, product) => sum + product.subtotal, 0),
        total: itens.reduce((sum, product) => sum + product.subtotal, 0) + orderState.payload.taxaEntrega
    }
})

watch(() => width.value, (newWidth) => resizebleColumns.value = useResponsiveColumns(columns, newWidth).value)

watch(() => orderState.payload.itens, (newVal) => {
    if (!newVal?.length) return
    disabledButton.value = newVal.map(product => product.quantidade).reduce((curr, prev) => curr + prev) < 1 ? true : false, { deep: true }
})

const updateOrderState = () => {
    orderState.payload = {
        ...orderState.payload,
        totalProdutos: computedOrderTotals.value.totalProdutos,
        total: computedOrderTotals.value.total,
        itens: computedOrderTotals.value.itens
    }
}

watch(() => orderState.tableData, () => updateOrderState(), { deep: true })

const updateData = (rowIndex, columnId, value) => {
    const oldRow = orderState.tableData[rowIndex]

    const updateTableData = (updateValue) =>
        orderState.tableData.map((row, index) =>
            index === rowIndex ? { ...oldRow, [columnId]: updateValue } : row
        )

    const actions = {
        preco: () => {
            console.log(oldRow[columnId])
            console.log(oldRow)
            console.log(columnId)
            const endRowLength = oldRow[columnId].length - 1
            updateTableData([{ qtd: oldRow[columnId][endRowLength].qtd, val: toFloat(value) }])
        },
        quantidade: () => updateTableData(value),
        precoEspecial: () => {
            if (value.payload) {
                const { payload, tableValue } = value
                createSpecialOffer(payload)
                return updateTableData(tableValue)
            }
            const endRowLength = oldRow[columnId].length - 1
            return updateTableData([{
                qtd: oldRow[columnId][endRowLength].qtd,
                val: toFloat(value)
            }])
        },
    }

    const newData = actions[columnId]
        ? actions[columnId]()
        : (toast.error('ação desconhecida'), orderState.tableData)

    orderState.tableData = newData
}

// Helper function to calculate order totals
// const calculateOrderTotals = (newData) => {
//     const itens = newData
//         .filter(product => product.quantidade > 0)
//         .map(product => {
//             const { id, quantidade } = product
//             const precoEspecial = product.precoEspecial
//             const precoProduto = product.preco
//             const preco = precoEspecial
//                 ? precoEspecial[precoEspecial.length - 1].val
//                 : precoProduto[precoProduto.length - 1].val
//             const subtotal = quantidade * preco

//             return {
//                 idProduto: id,
//                 quantidade,
//                 preco,
//                 subtotal,
//                 precoAcertado: null
//             }
//         })

//     try {
//         const totalProdutos = itens.reduce((sum, product) => sum + product.subtotal, 0)
//         const total = totalProdutos + orderState.payload.taxaEntrega

//         orderState.payload = {
//             ...orderState.payload,
//             totalProdutos,
//             total,
//             itens
//         }
//     } catch (error) {
//         console.log(error)
//         disabledButton.value = true
//         toast.error('Adicione ao menos um produto')
//     }
// }

const tableOptions = reactive(createTableOptions(
    orderState,
    globalFilter,
    sorting,
    resizebleColumns,
    clientId,
    updateData
))

const table = useVueTable(tableOptions)

onMounted(() => {
    const { products: rawProducts, distributorTaxes, distributor, address } = props.createOrderData
    const { taxaUnica: taxaEntrega } = distributorTaxes
    const { id: idDistribuidor, nome: distributorName } = distributor
    const { id: idEndereco, observacao, idCliente } = address
    const order = props.createOrderData.order

    const { updateOrder, orderPayload, orderStatus, products } = useDataToTableFormat(rawProducts, taxaEntrega, idDistribuidor, idEndereco, order)

    isUpdate.value = updateOrder
    orderState.payload = { ...orderState.payload, ...orderPayload }
    orderState.status = orderStatus
    orderState.tableData = products
    clientId.value = idCliente
    tableIdentifier.value = distributorName
    addressNote.value = observacao
    table.setPageSize(pageSizes.value[0])
})

</script>

<template>
    <div>
        <!-- <div class="relative flex flex-wrap items-center pb-1 justify-between gap-3 group">
            <div class="flex flex-col gap-1 w-full md:flex-row">
                <DebouncedInput :modelValue="globalFilter ?? ''"
                    @update:modelValue="value => (globalFilter = String(value))" placeholder="Todos os produtos..." />
                <SelectDistributor v-if="props.distributors" :distributors="props.distributors"
                    @update:distributor="handleDistributor" :default="`${orderState.payload.idDistribuidor}`">
                </SelectDistributor>
                <span v-else class="font-medium flex items-center justify-center text-info py-1 px-2 w-full">
                    {{ tableIdentifier }}
                </span>
            </div>
            <div class="flex flex-col gap-1 w-full md:flex-row pb-2">
                <button v-if="orderState.status"
                    :class="[orderState.status.classes.bg, orderState.status.label == 'Agendado' ? 'text-slate-700' : 'text-white',]"
                    class="relative font-semibold px-2 py-1 rounded-lg opacity-80 hover:opacity-100 "
                    @click="handleUpdateStatus">
                    <i v-if="orderState.status.label != 'Agendado' && orderState.status.label != 'Pendente'"
                        class="ri-edit-2-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
                        :class="orderState.status.classes.text"></i>
                    <i v-if="orderState.status.oldStatus"
                        class="ri-arrow-go-back-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
                        :class="orderState.status.classes.text"></i>
                    {{ orderState.status.label }}
                </button>
                <p class="text-sm font-semibold px-2 py-1 rounded-lg text-info ">
                    Cliente:
                    <span class="font-medium">
                        {{ props.createOrderData?.clientName ?? '' }}
                    </span>
                </p>
            </div>
        </div> -->
        <DataTableProducts.Header :distributors="props.distributors"
            :id-distribuidor="orderState.payload.idDistribuidor" :table-identifier="tableIdentifier"
            :status="orderState.status" :client-name="props.createOrderData.clientName" :global-filter="globalFilter"
            @update:global-filter="value => (globalFilter = String(value))" @update:status="handleUpdateStatus" />
        <DataTableProducts.Table :obs="orderState.payload.obs" :resizeble-columns="resizebleColumns" :table="table"
            @update:order-note="handleUpdateOrderNote" />
        <DataTableProducts.Footer :payload="orderState.payload" :is-update="isUpdate" :disabled-button="disabledButton"
            :address-note="addressNote" @update:payment-form="handlePayForm" @update:exchange="handleExchange"
            @update:scheduling="handleScheduling" @update:address-note="addressNote = $event"
            @callback:pedido="handleCallbackPedido" />
    </div>
</template>
