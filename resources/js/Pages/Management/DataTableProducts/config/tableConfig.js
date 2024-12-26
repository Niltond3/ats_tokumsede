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
    tableProductsState,
    globalFilter,
    sorting,
    resizebleColumns,
    clientId,
    updateData
) => ({
    get data() {
        return tableProductsState.tableData;
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
        editedRows: tableProductsState.editedRows,
        payload: tableProductsState.payload,
        tableData: tableProductsState.tableData,
    })),
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getFacetedMinMaxValues: getFacetedMinMaxValues(),
});
