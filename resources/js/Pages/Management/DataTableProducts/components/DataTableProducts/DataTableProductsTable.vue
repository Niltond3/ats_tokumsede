<script setup>
import { onMounted, watch } from 'vue'
// UI Components
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { FlexRender } from '@tanstack/vue-table'
import { DialogCreateOrderNote } from '..';


const props = defineProps({
    table: {
        type: Object,
        required: true
    },
    resizebleColumns: {
        type: Array,
        required: true
    },
    obs: {
        type: String,
        required: true
    }
})
const emits = defineEmits(['callback:order-note'])

const handleOrderNote = (value) => emits('callback:order-note', value)

onMounted(() => {
    console.log(props.table)
})

watch(props.table, (newValue) => { console.log(newValue) })

</script>

<template>
    <div class="border rounded-md border-gray-200 relative">
        <DialogCreateOrderNote v-if="obs" @callback:order-note="handleOrderNote" :order-note="props.obs" />
        <Table
            class="rounded-md [&_tbody]:h-[235px] [&_tbody]:table-fixed [&_tbody]:block [&_tbody]:overflow-y-auto [&_tbody]:overflow-x-hidden [&_tr]:table [&_tr]:w-full [&_tr]:table-fixed">
            <TableHeader class="bg-info rounded-md">
                <TableRow v-for="headerGroup in props.table.getHeaderGroups()" :key="headerGroup.id">
                    <TableHead v-for="header in headerGroup.headers" :key="header.id"
                        :style="{ width: header.getSize() + 'px' }">
                        <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                            :props="header.getContext()" />
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="props.table.getRowModel().rows?.length">
                    <TableRow v-for="row in props.table.getRowModel().rows" :key="row.id"
                        :data-state="row.getIsSelected() ? 'selected' : undefined">
                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id"
                            :style="{ width: cell.column.getSize() + 'px' }">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </TableCell>
                    </TableRow>
                </template>
                <template v-else>
                    <TableRow>
                        <TableCell :colspan="props.resizebleColumns.length" class="h-24 text-center">
                            No results.
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>
    </div>
</template>
