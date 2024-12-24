import {
    getCoreRowModel,
    getFacetedMinMaxValues,
    getFacetedRowModel,
    getPaginationRowModel,
    getFacetedUniqueValues,
    getFilteredRowModel,
    getSortedRowModel,
} from "@tanstack/vue-table";
import { computed } from "vue";

export const createTableOptions = (
    orderState,
    globalFilter,
    sorting,
    resizebleColumns,
    clientId,
    updateData
) => ({
    get data() {
        return orderState.tableData;
    },
    get columns() {
        return resizebleColumns.value;
    },
    state: {
        get globalFilter() {
            return globalFilter.value;
        },
        get sorting() {
            return sorting.value;
        },
        columnVisibility: {},
    },
    onSortingChange: (updaterOrValue) => {
        sorting.value =
            typeof updaterOrValue === "function"
                ? updaterOrValue(sorting.value)
                : updaterOrValue;
    },
    onGlobalFilterChange: (updaterOrValue) => {
        globalFilter.value =
            typeof updaterOrValue === "function"
                ? updaterOrValue(globalFilter.value)
                : updaterOrValue;
    },
    meta: computed(() => ({
        clientId: clientId.value,
        updateData,
        editedRows: orderState.editedRows,
        payload: orderState.payload,
        tableData: orderState.tableData,
    })),
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getFacetedMinMaxValues: getFacetedMinMaxValues(),
});
