import { computed } from "vue";
export const useResponsiveColumns = (columns, width) => {
    return computed(() => {
        if (width <= 768) {
            return columns.filter(
                (column) => column.id !== "img"
            )
        }
        return columns;
    });
};
