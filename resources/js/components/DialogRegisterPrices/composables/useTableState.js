import { ref, reactive } from "vue";
import { createTableOptions } from "@/components/DataTableProducts/config/tableConfig";
import { useVueTable } from "@tanstack/vue-table";
import { columns } from "@/components/DataTableProducts/config/Columns";

export function useTableState(tableProductsState, updateData) {
    const sorting = ref([]);
    const globalFilter = ref(null);
    const resizebleColumns = ref(
        columns.filter(
            (column) => column.id !== "quantidade" && column.id !== "actions"
        )
    );

    const tableOptions = reactive(
        createTableOptions(
            tableProductsState,
            globalFilter,
            sorting,
            resizebleColumns,
            { value: null },
            updateData
        )
    );

    const table = useVueTable(tableOptions);

    return {
        sorting,
        globalFilter,
        resizebleColumns,
        table,
        tableOptions,
    };
}
