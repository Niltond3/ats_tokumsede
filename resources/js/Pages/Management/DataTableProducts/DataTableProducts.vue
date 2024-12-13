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

// Custom Hooks
import { editRow } from './hooks/useEditRow'
import { dataTable } from './hooks/useDataTable'
import { payloadPedido } from '@/hooks/usePayloadPedido'

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

const props = defineProps({
    createOrderData: { type: null, required: false },
    distributors: { type: Array, required: false },
})

const [tableData, setTableData] = dataTable([])

const [editedRows, setEditedRows] = editRow()

const [payload, setPayload] = payloadPedido()

const isUpdate = ref(false)

const pageSizes = ref([50])

const sorting = ref([])

const status = ref(null)

const globalFilter = ref('')

const tableIdentifier = ref('')

const addressNote = ref('')

const disabledButton = ref(true)

const resizebleColumns = ref(columns)

const { toCurrency, toFloat } = formatMoney()

const { width } = useWindowSize()


const emit = defineEmits(['callback:payloadPedido'])

const handleCallbackPedido = () => {
    setPayload({ ...payload.value, observacao: addressNote.value })
    disabledButton.value = true
    emit('callback:payloadPedido', payload.value)
}

const handlePayForm = (value) => setPayload({ ...payload.value, formaPagamento: value })

const handleExchange = ({ value }) => {
    setPayload({ ...payload.value, trocoPara: parseFloat(value.split(' ')[1]) })
}

const handleScheduling = (date) => {
    if (date) {
        const { date: formattedDate, time } = dateToDayMonthYearFormat(date)

        const dataAgendada = formattedDate

        const horaInicio = time

        return setPayload({ ...payload.value, agendado: 1, dataAgendada, horaInicio })
    }
    return setPayload({ ...payload.value, agendado: 0, dataAgendada: '', horaInicio: '' })
}

const handleDistributor = (value) => setPayload({ ...payload.value, idDistribuidor: value })

const handleOrderNote = (value) => setPayload({ ...payload.value, obs: value })

const handleStatusChange = () => {
    if (status.value.label == 'Agendado') return toast.info('Pedido Agendado!')
    if (status.value.label == 'Pendente' && !status.value.oldStatus) return toast.info('Pedido Pendente!')
    if (status.value.label == 'Pendente' && status.value.oldStatus) {
        status.value = status.value.oldStatus
        setPayload({ ...payload.value, status: status.value.statusId })
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
        ...status.value,
        statusId: payload.value.status
    }

    status.value = { ...pendente, oldStatus }
    setPayload({ ...payload.value, status: 1 })
    return toast.info('Status Alterado!')
}

const dataToTable = (data) => {
    const { products, distributorTaxes: { taxaUnica: taxaEntrega }, distributor: { id: idDistribuidor, nome: distributorName }, address: { id: idEndereco, observacao } } = data

    tableIdentifier.value = distributorName

    addressNote.value = observacao

    const order = data.order

    if (order) {
        const { obs, itensPedido, total, formaPagamento: { id: formaPagamento }, trocoPara: orderTroco, agendado, dataAgendada, horaInicio, endereco: { observacao }, idEndereco, id: idPedido, status: orderStatus } = order
        isUpdate.value = true

        const newProducts = products.map(product => {
            const productToChange = itensPedido.filter(prod => prod.idProduto == product.id)[0]

            if (productToChange) return { ...product, preco: [{ qtd: product.preco[0].qtd, val: toFloat(productToChange.preco) }] }
            return product
        })

        setTableData(newProducts)

        const itens = itensPedido.map(item => {
            const { preco: itemPreco, qtd: quantidade, subtotal: itemSubtotal, id, precoAcertado, idProduto } = item
            const preco = toFloat(itemPreco)
            const subtotal = toFloat(itemSubtotal)

            return {
                idProduto,
                preco,
                precoAcertado,
                quantidade,
                subtotal
            }
        })

        status.value = orderStatus
        const trocoPara = toFloat(orderTroco)
        const totalProdutos = itens.map(product => parseFloat(product.subtotal)).reduce((curr, prev) => curr + prev);

        setPayload({ ...payload.value, formaPagamento, trocoPara, agendado, dataAgendada, horaInicio, obs, observacao, totalProdutos, total: toFloat(total), idEndereco, idDistribuidor, itens, idPedido, status: order.statusId })
        return
    }


    setTableData(products)

    setPayload({ ...payload.value, taxaEntrega, idDistribuidor, idEndereco })
}

watch(() => width.value, (newVal) => {
    if (newVal <= 448) {
        resizebleColumns.value = [...columns].filter(column => column.id !== "nome")
        return
    }
    resizebleColumns.value = columns

})

watch(() => props.createOrderData, (newVal) => {
    dataToTable(newVal)
})

watch(() => payload.value.itens, (newVal) => disabledButton.value = newVal.map(product => product.quantidade).reduce((curr, prev) => curr + prev) < 1 ? true : false)

const updateData = (rowIndex, columnId, value) => {
    const newData = columnId !== 'quantidade' ? [...tableData.value.map((row, index) => {
        if (index == rowIndex) {
            const oldRow = tableData.value[rowIndex]
            return {
                ...oldRow,
                [columnId]: [{ qtd: oldRow[columnId][0].qtd, val: toFloat(value) }]
            }
        }
        return row;
    })] : [...tableData.value.map((row, index) => {
        if (index == rowIndex) {
            const oldRow = tableData.value[rowIndex]
            return {
                ...oldRow,
                [columnId]: value
            }
        }
        return row;
    })]

    setTableData(newData)

    const itens = newData.map(product => {
        if (product.quantidade > 0) {
            const { id, preco, quantidade } = product
            return {
                idProduto: id,
                quantidade: quantidade,
                preco: preco[0].val,
                subtotal: quantidade * preco[0].val,
                precoAcertado: null,
            }
        }
        return null
    }).filter(x => x)

    const taxaEntrega = payload.value.taxaEntrega
    try {
        const totalProdutos = itens.map(product => product.subtotal).reduce((curr, prev) => curr + prev);
        const total = totalProdutos + taxaEntrega

        setPayload({ ...payload.value, totalProdutos, total, itens })
    } catch (error) {
        disabledButton.value = true
        toast.error('Adicione ao menos um produto')
    }

}

onMounted(() => {
    table.setPageSize(pageSizes.value[0])
    if (props.createOrderData) dataToTable(props.createOrderData)
})

const tableOptions = reactive({
    get data() { return tableData.value },
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
    meta: {
        updateData,
        editedRows,
        setEditedRows,
        payload
    },
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
                    @update:distributor="handleDistributor" :default="`${payload.idDistribuidor}`"></SelectDistributor>
                <span v-else class="font-medium flex items-center justify-center text-info py-1 px-2 w-full">
                    {{ tableIdentifier }}
                </span>
            </div>
            <div class="flex flex-col gap-1 w-full md:flex-row pb-2">
                <button v-if="status"
                    :class="[status.classes.bg, status.label == 'Agendado' ? 'text-slate-700' : 'text-white',]"
                    class="relative font-semibold px-2 py-1 rounded-lg opacity-80 hover:opacity-100 "
                    @click="handleStatusChange">
                    <i v-if="status.label != 'Agendado' && status.label != 'Pendente'"
                        class="ri-edit-2-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
                        :class="status.classes.text"></i>
                    <i v-if="status.oldStatus"
                        class="ri-arrow-go-back-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
                        :class="status.classes.text"></i>
                    {{ status.label }}
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
            <DialogCreateOrderNote @callback:order-note="handleOrderNote" :order-note="payload.obs">
            </DialogCreateOrderNote>
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
            <div class="flex flex-col gap-1 p-2">
                <Separator />
                <div class="flex items-center h-11 justify-around ">
                    <div class="flex gap-8">
                        <span class="text-sm font-medium relative text-info"> <span
                                class="absolute -top-7 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">Produtos</span>
                            {{
                                toCurrency(payload.totalProdutos)
                            }}</span>
                        <span class="text-sm font-medium relative text-info"> <span
                                class="absolute -top-7 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">Entrega</span>
                            {{
                                toCurrency(payload.taxaEntrega)
                            }}</span>
                        <span class="text-sm font-medium relative text-info"> <span
                                class="absolute -top-7 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">Total</span>
                            {{
                                toCurrency(payload.total)
                            }}</span>
                    </div>
                </div>
                <Separator label="Detalhes" class="z-100" />
                <div class="flex flex-wrap gap-2 p-2 sm:h-14 justify-center">
                    <SelectPayment @update:payment-form="handlePayForm" :default="payload.formaPagamento" />
                    <Separator orientation="vertical" class="" />
                    <ExchangeInput @update:exchange="handleExchange" :value="payload.trocoPara" />
                    <Separator orientation="vertical" class="hidden sm:block" />
                    <DateTimePicker @update:scheduling="handleScheduling"
                        :default:scheduling="dateToISOFormat(`${payload.dataAgendada} ${payload.horaInicio}`)" />
                </div>
            </div>
        </div>
        <div class="flex mt-3 gap-3">
            <div class="w-full relative">
                <Textarea
                    class="border rounded-md border-gray-200 min-h-11 h-11 focus-visible:ring-0 focus-visible:ring-offset-0 "
                    v-model:model-value="addressNote" />
                <span class="absolute text-xs text-muted-foreground left-2 -top-2 bg-white">observação do
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
