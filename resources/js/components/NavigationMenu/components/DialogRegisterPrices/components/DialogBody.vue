<script setup>
import { reactive, ref, watch } from 'vue';
import { useWindowSize } from '@vueuse/core'
import DataTableProducts from '@/Pages/Management/DataTableProducts/components/DataTableProducts';
import { useUpdateData } from '@/Pages/Management/DataTableProducts/composable/useUpdateData'
import { createTableOptions } from '@/Pages/Management/DataTableProducts/config/tableConfig';
import { columns } from '@/Pages/Management/DataTableProducts/config/Columns';
import { useVueTable } from '@tanstack/vue-table';
import { useTableProductsState } from '@/composables/tableProductsState'

const props = defineProps({
    'loading-products': { type: Boolean, Required: true },
    products: { type: Array, Required: true },
})

const resizebleColumns = ref(columns)
const globalFilter = ref('')
const sorting = ref([])
const clientId = ref(null)
const { width } = useWindowSize()

const tableProductsState = useTableProductsState()
const { updateData } = useUpdateData(tableProductsState)

watch(() => width.value, (newWidth) => resizebleColumns.value = useResponsiveColumns(columns, newWidth).value)

watch(() => props.products, newProducts => tableProductsState.tableData = newProducts)

const tableOptions = reactive(createTableOptions(
    tableProductsState,
    globalFilter,
    sorting,
    resizebleColumns,
    clientId,
    updateData
))
const table = useVueTable(tableOptions)

</script>

<template>
    <DataTableProducts.Table :table="table" :resizeble-columns="resizebleColumns" />
</template>
