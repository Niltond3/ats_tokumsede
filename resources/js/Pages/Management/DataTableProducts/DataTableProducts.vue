<script setup>
// Vue Core
import { ref, onMounted, reactive, watch } from 'vue'
import { useWindowSize } from '@vueuse/core'

// UI Components
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { Separator } from '@/components/ui/separator'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'

// Table Logic
import {
    FlexRender,
    getCoreRowModel,
    getFacetedMinMaxValues,
    getFacetedRowModel,
    getPaginationRowModel,
    getFacetedUniqueValues,
    getFilteredRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table'

//composable
import { useOrderState } from '@/composables/orderState'

// Configuration
import { columns } from './config/Columns'

// Components
import { SelectDistributor, DialogCreateOrderNote } from './components'
import SelectPayment from '@/components/orderComponents/SelectPayment.vue'
import ExchangeInput from '@/components/orderComponents/ExchangeInput.vue'
import DateTimePicker from '@/components/orderComponents/DateTimePicker.vue'
import DebouncedInput from './components/DebouncedInput.vue'

// Utilities
import { toast } from 'vue-sonner'
import { dateToDayMonthYearFormat, dateToISOFormat, formatMoney } from '@/util'
import renderToast from '@/components/renderPromiseToast'
import { computed } from 'vue'
import useDataToTableFormat from './composable/dataToTableFormat'

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

const { toCurrency, toFloat } = formatMoney()

const { width } = useWindowSize()

const emit = defineEmits(['callback:payloadPedido', 'update:specialOfferCreated'])

const handleCallbackPedido = () => {
    orderState.payload = { ...orderState.payload, observacao: addressNote.value }
    disabledButton.value = true
    emit('callback:payloadPedido', orderState.payload)
}

const createSpecialOffer = (payload) => {
    const url = 'preco'
    const promise = axios.post(url, payload)
    renderToast(promise, 'Salvando oferta ...', 'oferta salva com sucesso!', () => {
        emit('update:specialOfferCreated', true)
    }, 'Erro ao Salvar oferta!', (err) => {
        console.log(err)
    })
}

const handlePayForm = (value) => orderState.payload = { ...orderState.payload, formaPagamento: value }

const handleExchange = ({ value }) => orderState.payload = { ...orderState.payload, trocoPara: parseFloat(value.split(' ')[1]) }

const handleScheduling = (date) => {
    if (date) {
        const { date: formattedDate, time } = dateToDayMonthYearFormat(date)

        const dataAgendada = formattedDate

        const horaInicio = time

        return orderState.payload = { ...orderState.payload, agendado: 1, dataAgendada, horaInicio }
    }
    return orderState.payload = { ...orderState.payload, agendado: 0, dataAgendada: '', horaInicio: '' }
}

const handleDistributor = (value) => orderState.payload = { ...orderState.payload, idDistribuidor: value }

const handleOrderNote = (value) => orderState.payload = { ...orderState.payload, obs: value }

const handleStatusChange = () => {
    if (orderState.status.label == 'Agendado') return toast.info('Pedido Agendado!')
    if (orderState.status.label == 'Pendente' && !orderState.status.oldStatus) return toast.info('Pedido Pendente!')
    if (orderState.status.label == 'Pendente' && orderState.status.oldStatus) {
        orderState.status = orderState.status.oldStatus
        orderState.payload = { ...orderState.payload, status: orderState.status.statusId }
        return toast.info('Status Restaurado!')
    }

    const pendente = {
        label: 'Pendente',
        classes: {
            bg: 'bg-warning',
            text: 'text-warning',
            icon: 'ri-error-warning-fill'
        }

    }

    const oldStatus = {
        ...orderState.status,
        statusId: orderState.payload.status
    }

    orderState.status = { ...pendente, oldStatus }
    orderState.payload = { ...orderState.payload, status: 1 }
    return toast.info('Status Alterado!')
}

watch(() => width.value, (newVal) => {
    if (newVal <= 768) {
        resizebleColumns.value = [...columns].filter(column => !['nome', 'descricao'].includes(column.id))
    } else if (newVal <= 448) {
        resizebleColumns.value = [...columns].filter(column => column.id !== "nome")
    } else {
        resizebleColumns.value = columns
    }
})

watch(() => orderState.payload.itens, (newVal) => {
    console.log(newVal)
    if (newVal.length && Object.keys(newVal[0]).length) {
        console.log(newVal)
        disabledButton.value = newVal.map(product => product.quantidade).reduce((curr, prev) => curr + prev) < 1 ? true : false, { deep: true }
    }
})

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
    calculateOrderTotals(newData)
}

// Helper function to calculate order totals
const calculateOrderTotals = (newData) => {
    const itens = newData
        .filter(product => product.quantidade > 0)
        .map(product => {
            const { id, quantidade } = product
            const precoEspecial = product.precoEspecial
            const precoProduto = product.preco
            const preco = precoEspecial
                ? precoEspecial[precoEspecial.length - 1].val
                : precoProduto[precoProduto.length - 1].val
            const subtotal = quantidade * preco

            return {
                idProduto: id,
                quantidade,
                preco,
                subtotal,
                precoAcertado: null
            }
        })

    try {
        const totalProdutos = itens.reduce((sum, product) => sum + product.subtotal, 0)
        const total = totalProdutos + orderState.payload.taxaEntrega

        orderState.payload = {
            ...orderState.payload,
            totalProdutos,
            total,
            itens
        }
    } catch (error) {
        console.log(error)
        disabledButton.value = true
        toast.error('Adicione ao menos um produto')
    }
}

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


const tableOptions = reactive({
    get data() { return orderState.tableData },
    get columns() { return resizebleColumns.value },
    state: {
        get globalFilter() { return globalFilter.value },
        get sorting() { return sorting.value },
        columnVisibility: { /* globalSort: false,*/ },
    },
    onSortingChange: updaterOrValue => {
        sorting.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(sorting.value)
                : updaterOrValue
    },
    onGlobalFilterChange: (updaterOrValue) => {
        globalFilter.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(globalFilter.value)
                : updaterOrValue;
    },
    meta: computed(() => ({
        clientId: clientId.value,
        updateData,
        editedRows: orderState.editedRows,
        payload: orderState.payload,
        tableData: orderState.tableData
    })),
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(), //client side filtering
    getSortedRowModel: getSortedRowModel(), //client side sorting needed if you want to use sorting too.
    getPaginationRowModel: getPaginationRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getFacetedMinMaxValues: getFacetedMinMaxValues(),
})

const table = useVueTable(tableOptions)

</script>

<template>
    <div>
        <div class="relative flex flex-wrap items-center pt-4 pb-1 justify-between gap-3 group">
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
                    @click="handleStatusChange">
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
        </div>
        <div class="border rounded-md border-gray-200 relative">
            <DialogCreateOrderNote @callback:order-note="handleOrderNote" :order-note="orderState.payload.obs" />
            <Table
                class="rounded-md [&_tbody]:h-[235px] [&_tbody]:table-fixed [&_tbody]:block [&_tbody]:overflow-y-auto [&_tbody]:overflow-x-hidden [&_tr]:table [&_tr]:w-full [&_tr]:table-fixed">
                <TableHeader class="bg-info rounded-md">
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id"
                            :style="{ width: header.getSize() + 'px' }">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                            :data-state="row.getIsSelected() ? 'selected' : undefined">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id"
                                :style="{ width: cell.column.getSize() + 'px' }">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell :colspan="resizebleColumns.length" class="h-24 text-center">
                                No results.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
        <div class="flex mt-3 gap-3 flex-col sm:grid sm:grid-cols-3 ">
            <div class="flex items-center h-11 justify-around ">
                <div class="w-full flex flex-col items-center justify-center">
                    <Separator class="mt-1 mb-[0.35rem]" />
                    <div class="flex gap-8">
                        <span class="text-sm font-medium relative text-info"> <span
                                class="absolute -top-5 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">Produtos</span>
                            {{
                                toCurrency(orderState.payload.totalProdutos)
                            }}</span>
                        <span class="text-sm font-medium relative text-info"> <span
                                class="absolute -top-5 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">Entrega</span>
                            {{
                                toCurrency(orderState.payload.taxaEntrega)
                            }}</span>
                        <span class="text-sm font-medium relative text-info"> <span
                                class="absolute -top-5 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">Total</span>
                            {{
                                toCurrency(orderState.payload.total)
                            }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 px-2 pb-2 sm:h-14 justify-center">
                <Separator label="Detalhes" class="z-100 my-1" />
                <SelectPayment @update:payment-form="handlePayForm" :default="orderState.payload.formaPagamento" />
                <Separator orientation="vertical" class="" />
                <ExchangeInput @update:exchange="handleExchange" :value="orderState.payload.trocoPara" />
                <Separator orientation="vertical" class="hidden sm:block" />
                <DateTimePicker @update:scheduling="handleScheduling"
                    :default:scheduling="dateToISOFormat(`${orderState.payload.dataAgendada} ${orderState.payload.horaInicio}`)" />
            </div>
            <div class="w-full sm:w-1/2 relative flex gap-1 p-2">
                <Textarea
                    class="border rounded-md border-gray-200 min-h-11 h-11 sm:min-h-16 focus-visible:ring-0 focus-visible:ring-offset-0 "
                    v-model:model-value="addressNote" />
                <span class="absolute text-xs text-muted-foreground left-2 -top-0 bg-white">observação do
                    endereço</span>
            </div>
            <Button :disabled="disabledButton" type="submit"
                class="border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/60 disabled:hover:bg-info/60 disabled:cursor-not-allowed "
                @click="handleCallbackPedido">
                <span v-if="isUpdate"> Salvar </span>
                <span v-else> Cadastrar </span>
            </Button>
        </div>
    </div>
</template>
