<script setup>
// Vue core
import { ref, watch, computed, onMounted } from 'vue'
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
import { useUpdateData } from "@/Pages/Management/DataTableProducts/composable/useUpdateData"
import { useTableProductsState } from "@/composables/tableProductsState"

// Local utilities and composables
import { useDialogControls } from './composables/useDialogControlers'
import { formatProducts } from './util/productFormatters'
import { useTableState } from './composables/useTableState'
import { useDistributorsAndProducts } from './composables/useDistributorsAndProducts'
import { useResponsiveColumns } from './composables/useResponsiveColumns'

const props = defineProps({
    addressId: { type: [String, Number], required: false, default: null },
    isOpen: { type: Boolean, required: false, default: null },
    toggleDialog: { type: Function, required: false, default: null }
})

const { width } = useWindowSize()
const selectedDistributorId = ref(null)
const tableProductsState = useTableProductsState()
const { updateData } = useUpdateData(tableProductsState)

const {
    distributor,
    distributors,
    loadingDistributors,
    loadingProducts,
    fetchDistributor,
    fetchDistributors,
    fetchProductsForDistributor
} = useDistributorsAndProducts()

const {
    globalFilter,
    resizebleColumns,
    table
} = useTableState(tableProductsState, updateData)

// In setup:
const { dialogControls } = useDialogControls({ isOpen: props.isOpen, toggleDialog: props.toggleDialog })


const emits = defineEmits(["on:create", 'update:dialogOpen'])

const loadDistributor = async (addressId) => {
    if (!addressId) return
    const response = await fetchDistributor(addressId)
    if (response) return response
}

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
    globalFilter: null,
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
    console.log(op)
    if (!op) {
        resetDialogValues();
        emits('update:dialogOpen', false);
        return dialogControls.value.toggleDialog();
    }
    resetDialogValues();
    const action = props.addressId ?
        () => {
            console.log('action = loadDistributorProducts', props.addressId)
            console.log(loadDistributor(props.addressId))
            loadDistributorProducts(distributor.id)
        } : () => {
            console.log('action = fetchDistributors')
            fetchDistributors()
        };

    action();
    dialogControls.value.toggleDialog();
}

onMounted(() => {
    if (!props.addressId) {
        resetDialogValues();
        fetchDistributors()
    }
})

watch(() => dialogControls.value.isOpen, async (newVal) => {
    console.log(newVal)
})

watch(() => props.addressId, async (newVal) => {
    if (newVal) {
        const distribuidor = await loadDistributor(newVal)
        await loadDistributorProducts(distribuidor.id)

    }
})

</script>

<template>
    <Dialog :open="dialogControls.isOpen" @update:open="handleDialogOpen">
        <slot name="trigger" />
        <DialogContent class="flex flex-col gap-2">
            <DialogHeader :loading-distributors="loadingDistributors" :distributors="distributors"
                :distributor-id="selectedDistributorId" :global-filter="globalFilter"
                @update:distributor="loadDistributorProducts"
                @update:global-filter="value => (globalFilter = String(value))" />
            <DialogBody :table="table" :resizeble-columns="resizebleColumns"
                @update:table-data="handleUpdateTableData" />
            <DialogFooter :loading-products="loadingProducts" :products="tableProductsState.tableData"
                :distributor-id="selectedDistributorId" @sucess-update="loadDistributorProducts" />
        </DialogContent>
    </Dialog>
</template>
