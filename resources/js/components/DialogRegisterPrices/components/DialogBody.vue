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
    table: { type: null, Required: true },
    resizebleColumns: { type: Array, Required: true },
})

</script>

<template>
    <div>
        <DataTableProducts.Table :table="table" :resizeble-columns="resizebleColumns" />
    </div>
</template>

<style scoped>
.space-y-2>*+* {
    margin-top: 0.5rem;
}
</style>
