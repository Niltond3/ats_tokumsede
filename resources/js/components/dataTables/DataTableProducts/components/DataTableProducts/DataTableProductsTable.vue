<script setup>
// UI Components
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { FlexRender } from '@tanstack/vue-table';
import { DialogCreateOrderNote } from '..';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

const props = defineProps({
  products: {
    type: Array,
    required: true,
  },
  table: {
    type: Object,
    required: true,
  },
  resizebleColumns: {
    type: Array,
    required: true,
  },
  obs: {
    type: String,
    required: false,
  },
});

const emits = defineEmits(['update:order-note']);
</script>

<template>
  <div class="border rounded-md border-gray-200 relative">
    <DialogCreateOrderNote
      v-if="obs || obs == ''"
      :order-note="props.obs"
      @callback:order-note="(value) => emits('update:order-note', value)"
    />
    <Table
      class="rounded-md [&_tbody]:h-[235px] [&_tbody]:table-fixed [&_tbody]:block [&_tbody]:overflow-y-auto [&_tbody]:overflow-x-hidden [&_tr]:table [&_tr]:w-full [&_tr]:table-fixed"
    >
      <!-- <TooltipProvider :delay-duration="100">
        <Tooltip>
          <TooltipTrigger as-child>
            <TableHeader
              class="bg-info rounded-md"
              @click="
                () => {
                  console.log(props.products);
                }
              "
            >
              <TableRow v-for="headerGroup in props.table.getHeaderGroups()" :key="headerGroup.id">
                <TableHead
                  v-for="header in headerGroup.headers"
                  :key="header.id"
                  :style="{ width: header.getSize() + 'px' }"
                >
                  <FlexRender
                    v-if="!header.isPlaceholder"
                    :render="header.column.columnDef.header"
                    :props="header.getContext()"
                  />
                </TableHead>
              </TableRow>
            </TableHeader>
          </TooltipTrigger>
          <TooltipContent class="border-none text-white bg-info font-bold flex gap-2">
            <i class="ri-file-copy-2-fill"></i>
            clique para copiar os preços
          </TooltipContent>
        </Tooltip>
      </TooltipProvider> -->
      <TableHeader
        class="bg-info rounded-md"
        @click="
          () => {
            console.log(props.products);
          }
        "
      >
        <TableRow v-for="headerGroup in props.table.getHeaderGroups()" :key="headerGroup.id">
          <TableHead
            v-for="header in headerGroup.headers"
            :key="header.id"
            :style="{ width: header.getSize() + 'px' }"
          >
            <FlexRender
              v-if="!header.isPlaceholder"
              :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template v-if="props.table.getRowModel().rows?.length">
          <TableRow
            v-for="row in props.table.getRowModel().rows"
            :key="row.id"
            :data-state="row.getIsSelected() ? 'selected' : undefined"
          >
            <TableCell
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
              :style="{ width: cell.column.getSize() + 'px' }"
            >
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
