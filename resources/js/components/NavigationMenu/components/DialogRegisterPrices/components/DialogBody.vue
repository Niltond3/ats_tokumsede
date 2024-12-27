<script setup>
import { reactive, ref, watch } from 'vue';
import { useWindowSize } from '@vueuse/core'
import DataTableProducts from '@/Pages/Management/DataTableProducts/components/DataTableProducts';
import { useUpdateData } from '@/Pages/Management/DataTableProducts/composable/useUpdateData'
import { createTableOptions } from '@/Pages/Management/DataTableProducts/config/tableConfig';
import { columns } from '@/Pages/Management/DataTableProducts/config/Columns';
import { useVueTable } from '@tanstack/vue-table';
import { useTableProductsState } from '@/composables/tableProductsState'
import { Skeleton } from '@/components/ui/skeleton'

const props = defineProps({
    loadingProducts: { type: Boolean, Required: true },
    products: { type: Array, Required: true },
})

const emits = defineEmits(['update:table-data'])

const registerPricesColumns = columns.filter(column =>
    column.id !== 'quantidade' && column.id !== 'actions'
)

const resizebleColumns = ref(registerPricesColumns)
const globalFilter = ref('')
const sorting = ref([])
const clientId = ref(null)
const { width } = useWindowSize()

const tableProductsState = useTableProductsState()
const { updateData } = useUpdateData(tableProductsState)

watch(() => width.value, (newWidth) => resizebleColumns.value = useResponsiveColumns(columns, newWidth).value)

watch(() => props.products, newProducts => {
    console.log(newProducts)
    tableProductsState.tableData = newProducts.map(product => ({
        id: product.id,
        img: product.img,
        nome: product.nome,
        preco: [{
            qtd: product.qtdMin,
            val: product.valor
        }],
    }))
    console.log(tableProductsState.tableData)
})

watch(() => tableProductsState.tableData, newTableData => emits('update:table-data', newTableData))

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
    <div>
        <template v-if="props.loadingProducts">
            <div class="space-y-2">
                <Skeleton class="h-4 w-full" />
                <Skeleton class="h-4 w-[90%]" />
                <Skeleton class="h-4 w-[80%]" />
                <Skeleton class="h-4 w-[95%]" />
                <Skeleton class="h-4 w-[85%]" />
            </div>
        </template>
        <template v-else>
            <DataTableProducts.Table :table="table" :resizeble-columns="resizebleColumns" />
        </template>
    </div>
</template>

<style scoped>
.space-y-2>*+* {
    margin-top: 0.5rem;
}
</style>
