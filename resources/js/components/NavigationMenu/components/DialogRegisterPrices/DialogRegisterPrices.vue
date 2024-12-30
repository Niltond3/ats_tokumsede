<script setup>
// Vue core
import { ref, watch } from 'vue'
import { useWindowSize } from '@vueuse/core'

// UI Components
import {
    Dialog,
    DialogContent
} from '@/components/ui/dialog'
import DialogTrigger from '../DialogTrigger.vue'
import DialogHeader from './components/DialogHeader.vue'
import DialogBody from './components/DialogBody.vue'
import DialogFooter from './components/DialogFooter.vue'

// Table related
import { useUpdateData } from "@/Pages/Management/DataTableProducts/composable/useUpdateData"
import { useTableProductsState } from "@/composables/tableProductsState"

// Local utilities and composables
import { dialogState } from '@/hooks/useToggleDialog'
import { formatProducts } from './util/productFormatters'
import { useTableState } from './composables/useTableState'
import { useDistributorsAndProducts } from './composables/useDistributorsAndProducts'
import { useResponsiveColumns } from './composables/useResponsiveColumns'


const { width } = useWindowSize()
const selectedDistributorId = ref(null)
const tableProductsState = useTableProductsState()
const { updateData } = useUpdateData(tableProductsState)
const { isOpen, toggleDialog } = dialogState()

const {
    distributors,
    loadingDistributors,
    loadingProducts,
    fetchDistributors,
    fetchProductsForDistributor
} = useDistributorsAndProducts()

const {
    globalFilter,
    resizebleColumns,
    table
} = useTableState(tableProductsState, updateData)

const emits = defineEmits(["on:create", 'update:dialogOpen'])

const loadDistributorProducts = async (distributorId) => {
    if (!distributorId) return
    tableProductsState.tableData = []
    selectedDistributorId.value = distributorId

    const response = await fetchProductsForDistributor(distributorId)
    if (response) {
        globalFilter.value = ""
        tableProductsState.tableData = formatProducts(response)
    }
}

const getDefaultValues = () => ({
    selectedDistributorId: null,
    globalFilter: '',
    tableData: []
})

const resetDialogValues = () => {
    selectedDistributorId.value = getDefaultValues().selectedDistributorId
    globalFilter.value = getDefaultValues().globalFilter
    tableProductsState.tableData = getDefaultValues().tableData
}

watch(
    () => width.value,
    (newWidth) => resizebleColumns.value = useResponsiveColumns(resizebleColumns.value, newWidth).value
)


const handleDialogOpen = (op) => {
    if (!op) {
        resetDialogValues()
        emits('update:dialogOpen', false)
    } else {
        fetchDistributors()
    }
    toggleDialog()
}


</script>

<template>
    <Dialog :open="isOpen" @update:open="handleDialogOpen">
        <DialogTrigger icon="ri-price-tag-3-fill" title="Preço" />
        <DialogContent class="flex flex-col gap-2">
            <DialogHeader :loading-distributors="loadingDistributors" :distributors="distributors"
                :global-filter="globalFilter" @update:distributor="loadDistributorProducts"
                @update:global-filter="value => (globalFilter = String(value))" />
            <DialogBody :table="table" :resizeble-columns="resizebleColumns"
                @update:table-data="handleUpdateTableData" />
            <DialogFooter :loading-products="loadingProducts" :products="tableProductsState.tableData"
                :distributor-id="selectedDistributorId" @sucess-update="loadDistributorProducts" />
        </DialogContent>
    </Dialog>
</template>
