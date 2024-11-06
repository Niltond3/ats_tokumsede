<script setup>
import { ref, onMounted, reactive, watch } from 'vue'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
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
import { rankItem } from '@tanstack/match-sorter-utils'
import { Separator } from '@/components/ui/separator'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { editRow } from './useEditRow'
import { dataTable } from './useDataTable'
import { payloadPedido } from './usePayloadPedido'
import { formatMoney } from '@/util'
import SelectPayment from './SelectPayment.vue'
import ExchangeInput from './ExchangeInput.vue'
import DateTimePicker from './DateTimePicker.vue'
import DialogCreateOrderNote from './DialogCreateOrderNote.vue'

const props = defineProps({
    columns: { type: null, required: false },
    createOrderData: { type: null, required: false },
})

const [tableData, setTableData] = dataTable([])

const [editedRows, setEditedRows] = editRow()

const [payload, setPayload] = payloadPedido()

const { toCurrency } = formatMoney()

const emit = defineEmits(['callback:payloadPedido'])

const pageSizes = ref([50])

const sorting = ref([])

const globalFilter = ref('')

const tableIdentifier = ref('')

const addressNote = ref('')

// Define a custom fuzzy filter function that will apply ranking info to rows (using match-sorter utils)
const fuzzyFilter = (row, columnId, value, addMeta) => {

    //get column keys in row
    const columnKeys = row.getAllCells().map((x) => x.column.id);

    //map through column keys and rank each column, producing an array
    const rowsValues = columnKeys.map((k) => {
        const rowVal = row.getValue(k);
        if (k != 'globalSort' && k != 'enderecos' && k != 'actions' && rowVal !== null) return rowVal.toString().toLowerCase()
        return null
    }).filter((x) => x).toString().replace(/,/g, " ");

    const rankedValue = rankItem(rowsValues, value, {
        threshold: 0.1,
        keepDiacritics: true
    });

    //calculate aggregate rank
    const aggregateRank = rankedValue.rank;

    //create an object to add to columnFiltersMeta
    const metaItem = { aggregateRank, rankedValues: rankedValue };

    //add filter results to meta
    addMeta(metaItem);

    //set logic for does the filter pass
    const doesItemPass = rankedValue.rank >= 1.01;

    if (doesItemPass) { console.log(metaItem) }

    // Return if the item should be filtered in/out
    return doesItemPass; //itemRank.passed;
}

const handleCallbackPedido = () => {
    setPayload({ ...payload.value, observacao: addressNote.value })
    emit('callback:payloadPedido', payload.value)
}

const handleExchange = ({ value }) => setPayload({ ...payload.value, trocoPara: parseFloat(value) })

const handlePayForm = (value) => setPayload({ ...payload.value, formaPagamento: value })

const handleScheduling = (value) => {
    if (value) {
        const dataAgendada = `${value.getDate()}-${value.getMonth(2) + 1}-${value.getFullYear()}`
        const horaInicio = `${value.getHours(2)}:${value.getMinutes(2)}:00`
        setPayload({ ...payload.value, agendado: 1, dataAgendada, horaInicio })
        return
    }
    setPayload({ ...payload.value, agendado: 0, dataAgendada: '', horaInicio: '' })
}

const handleOrderNote = (value) => setPayload({ ...payload.value, obs: value })

watch(() => props.createOrderData, (newVal) => {
    const { products, distributorTaxes: { taxaUnica: taxaEntrega }, distributor: { id: idDistribuidor, nome: distributorName }, address: { id: idEndereco, observacao } } = newVal
    console.log(newVal)
    setTableData(products)

    tableIdentifier.value = distributorName

    addressNote.value = observacao

    setPayload({ ...payload.value, taxaEntrega, idDistribuidor, idEndereco })
})

const updateData = (rowIndex, columnId, value) => {
    const newData = columnId !== 'quantidade' ? [...tableData.value.map((row, index) => {
        if (index == rowIndex) {
            const oldRow = tableData.value[rowIndex]
            return {
                ...oldRow,
                [columnId]: [{ qtd: oldRow[columnId][0].qtd, val: value }]
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

    const cartProducts = newData.map(product => {
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


    const totalProdutos = cartProducts.map(product => product.subtotal).reduce((curr, prev) => curr + prev);

    const taxaEntrega = payload.value.taxaEntrega

    setPayload({ ...payload.value, totalProdutos, total: totalProdutos + taxaEntrega, itens: cartProducts })

    console.log(payload.value)
}

onMounted(() => {
    table.setPageSize(pageSizes.value[0])
})

const tableOptions = reactive({
    get data() { return tableData.value },
    get columns() { return props.columns },
    filterFns: {
        fuzzy: fuzzyFilter, //define as a filter function that can be used in column definitions
    },
    state: {
        get globalFilter() { return globalFilter.value },
        get sorting() {
            return sorting.value
        },
        columnVisibility: {
            // globalSort: false,
        },

    },
    onSortingChange: updaterOrValue => {
        sorting.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(sorting.value)
                : updaterOrValue
    },
    meta: {
        updateData,
        editedRows,
        setEditedRows
    },
    globalFilterFn: 'fuzzy', //apply fuzzy filter to the global filter (most common use case for fuzzy filter)
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(), //client side filtering
    getSortedRowModel: getSortedRowModel(), //client side sorting needed if you want to use sorting too.
    //no need to pass pageCount or rowCount with client-side pagination as it is calculated automatically
    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onGlobalFilterChange: updaterOrValue => valueUpdater(updaterOrValue, globalFilter),
    getPaginationRowModel: getPaginationRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getFacetedMinMaxValues: getFacetedMinMaxValues(),
})

const table = useVueTable(tableOptions)

</script>

<template>
    <div>
        <div class="flex items-center py-4 justify-between">
            <Input class="max-w-sm" placeholder="Pesquisar..." :modelValue="globalFilter ?? ''"
                @update:modelValue="value => (globalFilter = String(value))" />
            <span>{{ tableIdentifier }}</span>
        </div>
        <div class="border rounded-md border-gray-200 relative">
            <DialogCreateOrderNote @callback:order-note="(callback) => handleOrderNote(callback)">
            </DialogCreateOrderNote>
            <Table
                class="rounded-md [&_tbody]:h-[235px] [&_tbody]:table-fixed [&_tbody]:block [&_tbody]:overflow-y-auto [&_tbody]:overflow-x-hidden [&_tr]:table [&_tr]:w-full [&_tr]:table-fixed">
                <TableHeader class="bg-info rounded-md">
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                            :data-state="row.getIsSelected() ? 'selected' : undefined">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell :colspan="columns.length" class="h-24 text-center">
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
                <div class="flex p-2 gap-4 h-14 justify-center">
                    <SelectPayment @update:payment-form="handlePayForm" />
                    <Separator orientation="vertical" />
                    <ExchangeInput @update:exchange="handleExchange" />
                    <Separator orientation="vertical" />
                    <DateTimePicker @update:scheduling="handleScheduling" />
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
            <Button type="submit"
                class="border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all"
                @click="handleCallbackPedido"> Cadastrar Pedido </Button>
        </div>
    </div>
</template>
