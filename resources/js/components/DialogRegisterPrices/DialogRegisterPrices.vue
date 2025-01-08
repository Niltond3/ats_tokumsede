<script setup>
// Vue core
import { ref, watch } from 'vue'
import { useWindowSize } from '@vueuse/core'

// UI Components
import {
    Dialog,
    DialogContent
} from '@/components/ui/dialog'
import DialogHeader from './components/DialogHeader.vue'
import DialogBody from './components/DialogBody.vue'
import DialogFooter from './components/DialogFooter.vue'

// Table related
import { useUpdateData } from "@/components/DataTableProducts/composable/useUpdateData"
import { useTableProductsState } from "@/composables/tableProductsState"

// Local utilities and composables
import { useTableState } from './composables/useTableState'
import { useDistributorsAndProducts } from './composables/useDistributorsAndProducts'
import { useResponsiveColumns } from './composables/useResponsiveColumns'
import { getClient } from '@/services/api/client'
import { utf8Decode } from '@/util'

const props = defineProps({
    clientId: { type: [String, Number], required: false, default: null },
    addressId: { type: [String, Number], required: false, default: null },
    isOpen: { type: Boolean, required: false, default: null },
    toggleDialog: { type: Function, required: false, default: null },
    title: { type: String, required: false, default: 'Cadastro de Preços' },
    description: { type: String, required: false, default: 'Atualização dos preços padrões de cada distribuidor' },
})

const { width } = useWindowSize()
const selectedDistributorId = ref(null)
const clientName = ref(null)
const tableProductsState = useTableProductsState()
const { updateData } = useUpdateData(tableProductsState)

const {
    tableIdentifier,
    distributor,
    distributors,
    loadingDistributors,
    loadingProducts,
    fetchDistributor,
    fetchDistributors,
    fetchProductsForDistributor,
} = useDistributorsAndProducts()

const {
    globalFilter,
    resizebleColumns,
    table
} = useTableState(tableProductsState, updateData)

const emits = defineEmits(["on:create"])

const getDefaultValues = () => ({
    selectedDistributorId: null,
    globalFilter: null,
    tableData: [],
    distributor: null,
    distributors: [],
    loadingDistributors: true,
    loadingProducts: true,
    sorting: [],
    payload: {
        formaPagamento: "1",
        trocoPara: 0,
        agendado: 0,
        dataAgendada: "",
        horaInicio: "",
        horaFim: "",
        obs: "",
        observacao: "",
        taxaEntrega: 0,
        totalProdutos: 0,
        total: 0,
        idEndereco: 0,
        idDistribuidor: 0,
        itens: [{}],
        origem: null
    }
})

const resetDialogValues = () => {
    // Reset main dialog values
    selectedDistributorId.value = getDefaultValues().selectedDistributorId
    globalFilter.value = getDefaultValues().globalFilter
    tableProductsState.tableData = getDefaultValues().tableData

    // Reset distributors and products state
    distributor.value = getDefaultValues().distributor
    distributors.value = getDefaultValues().distributors
    loadingDistributors.value = getDefaultValues().loadingDistributors
    loadingProducts.value = getDefaultValues().loadingProducts

    // Reset table products state payload
    Object.assign(tableProductsState.payload, getDefaultValues().payload)
    tableProductsState.status = null
    tableProductsState.editedRows = null
}

const loadDistributorProducts = async (distributorId, clientId) => {
    if (!distributorId) return

    globalFilter.value = getDefaultValues().globalFilter
    tableProductsState.tableData = getDefaultValues().tableData

    selectedDistributorId.value = distributorId

    const response = await fetchProductsForDistributor(distributorId, clientId)
    if (response) {
        globalFilter.value = ""
        tableProductsState.tableData = response
    }
}

watch(
    () => width.value,
    (newWidth) => resizebleColumns.value = useResponsiveColumns(resizebleColumns.value, newWidth).value
)

watch(() => props.isOpen, async (newVal) => {
    resetDialogValues();
    if (!newVal) return
    const action = props.addressId ?
        async () => {
            distributors.value = null
            const clientRequest = await getClient(props.clientId)
            clientName.value = utf8Decode(clientRequest.data.nome)
            await fetchDistributor(props.addressId)
            loadDistributorProducts(distributor.value.id, props.clientId)
        } : () => fetchDistributors();
    action();
})

const handleDialogOpen = (op) => {
    resetDialogValues();
    if (!op) return props.toggleDialog()
    props.toggleDialog()
}

</script>

<template>
    <Dialog :open="props.isOpen" @update:open="handleDialogOpen">
        <slot name="trigger" />
        <DialogContent class="flex flex-col gap-2">
            <DialogHeader :loading-distributors="loadingDistributors" :distributors="distributors"
                :distributor-id="selectedDistributorId" :global-filter="globalFilter" :title="title"
                :description="description" :tableIdentifier="tableIdentifier" :client-name="clientName"
                @update:distributor="loadDistributorProducts"
                @update:global-filter="value => (globalFilter = String(value))" />
            <DialogBody :table="table" :resizeble-columns="resizebleColumns"
                @update:table-data="handleUpdateTableData" />
            <DialogFooter :loading-products="loadingProducts" :products="tableProductsState.tableData"
                :clientId="clientId" :distributor-id="selectedDistributorId" @sucess-update="loadDistributorProducts" />
        </DialogContent>
    </Dialog>
</template>
