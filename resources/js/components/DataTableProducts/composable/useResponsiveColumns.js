import { computed } from "vue";
export const useResponsiveColumns = (columns, width) => {
    return computed(() => {
        if (width <= 768) {
            return columns.filter(
                (column) => !["nome", "descricao"].includes(column.id)
            );
        }
        if (width <= 448) {
            return columns.filter((column) => column.id !== "nome");
        }
        return columns;
    });
};
