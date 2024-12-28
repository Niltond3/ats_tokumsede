<script setup>
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
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';


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
        required: false
    }
})
const emits = defineEmits(['update:order-note'])

</script>

<template>
    <div class="border rounded-md border-gray-200 relative">
        <DialogCreateOrderNote v-if="obs || obs == ''" @callback:order-note="value => emits('update:order-note', value)"
            :order-note="props.obs" />
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
                        <TableCell :colspan="props.resizebleColumns.length" class="h-[14.71rem]">
                            <Skeleton class="w-full h-full" />
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>
    </div>
</template>
