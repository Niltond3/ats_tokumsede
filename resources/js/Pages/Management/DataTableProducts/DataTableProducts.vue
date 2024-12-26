<script setup>
// Vue Core
import { ref, onMounted, reactive, watch, computed } from 'vue'
import { useWindowSize } from '@vueuse/core'

// Table Logic
import { useVueTable } from '@tanstack/vue-table'

//composable
import { useTableProductsState } from '@/composables/tableProductsState'
import { useResponsiveColumns } from './composable/useResponsiveColumns'
import { useEventHandlers } from './composable/useEventHandlers'
import { useUpdateData } from './composable/useUpdateData'

// Configuration
import { columns } from './config/Columns'
import { createTableOptions } from './config/tableConfig'

// Utilities
import { formatMoney } from '@/util'
import useDataToTableFormat from './composable/dataToTableFormat'
import DataTableProducts from './components/DataTableProducts'

const props = defineProps({
    createOrderData: { type: null, required: false },
    distributors: { type: Array, required: false },
})

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

const tableProductsState = useTableProductsState()
const { handleDistributor, handleCallbackPedido, handleExchange, handlePayForm, handleScheduling, handleUpdateOrderNote, handleUpdateStatus } = useEventHandlers(tableProductsState, emit, addressNote, disabledButton)
const { updateData } = useUpdateData(tableProductsState)

const computedOrderTotals = computed(() => {
    const itens = tableProductsState.tableData
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
        total: itens.reduce((sum, product) => sum + product.subtotal, 0) + tableProductsState.payload.taxaEntrega
    }
})

const updateTableProductsState = () => {
    tableProductsState.payload = {
        ...tableProductsState.payload,
        totalProdutos: computedOrderTotals.value.totalProdutos,
        total: computedOrderTotals.value.total,
        itens: computedOrderTotals.value.itens
    }
}

watch(() => tableProductsState.tableData, () => updateTableProductsState(), { deep: true })

watch(() => width.value, (newWidth) => resizebleColumns.value = useResponsiveColumns(columns, newWidth).value)

watch(() => tableProductsState.payload.itens, (newVal) => {
    if (!newVal?.length) return
    disabledButton.value = newVal.map(product => product.quantidade).reduce((curr, prev) => curr + prev) < 1 ? true : false, { deep: true }
})


const tableOptions = reactive(createTableOptions(
    tableProductsState,
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
    tableProductsState.payload = { ...tableProductsState.payload, ...orderPayload }
    tableProductsState.status = orderStatus
    tableProductsState.tableData = products
    clientId.value = idCliente
    tableIdentifier.value = distributorName
    addressNote.value = observacao
    table.setPageSize(pageSizes.value[0])
})

</script>

<template>
    <div>
        <DataTableProducts.Header :distributors="props.distributors"
            :id-distribuidor="tableProductsState.payload.idDistribuidor" :table-identifier="tableIdentifier"
            :status="tableProductsState.status" :client-name="props.createOrderData.clientName"
            :global-filter="globalFilter" @update:global-filter="value => (globalFilter = String(value))"
            @update:status="handleUpdateStatus" @update:distributor="handleDistributor" />
        <DataTableProducts.Table :obs="tableProductsState.payload.obs" :resizeble-columns="resizebleColumns"
            :table="table" @update:order-note="handleUpdateOrderNote" />
        <DataTableProducts.Footer :payload="tableProductsState.payload" :is-update="isUpdate"
            :disabled-button="disabledButton" :address-note="addressNote" @update:payment-form="handlePayForm"
            @update:exchange="handleExchange" @update:scheduling="handleScheduling"
            @update:address-note="addressNote = $event" @callback:pedido="handleCallbackPedido" />
    </div>
</template>
