import { rankItem } from "@tanstack/match-sorter-utils";
export const tableProdutcs = () => {
    const fuzzyFilter = (row, columnId, value, addMeta) => {
        // Handle global filter
        if (columnId === "globalFilter") {
            const searchableColumns = ["nome", "preco"];
            return searchableColumns.some((col) => {
                const rowValue = row.getValue(col);
                const rowEndArray = rowValue.length - 1;
                const itemValue =
                    col === "preco"
                        ? rowValue[rowEndArray].val.toString()
                        : rowValue.toString();

                const rankedItem = rankItem(itemValue, value, {
                    threshold: 0.2,
                    keepDiacritics: true,
                });
                return rankedItem.rank > -1;
            });
        }

        // Original column filtering logic
        if (columnId !== "nome" && columnId !== "preco") return true;

        const rowValue = row.getValue(columnId);
        const rowEndArray = rowValue.length - 1;

        const itemValue =
            columnId === "preco"
                ? rowValue[rowEndArray].val.toString()
                : rowValue.toString();

        const rankedItem = rankItem(itemValue, value, {
            threshold: 0.2,
            keepDiacritics: true,
        });

        addMeta({
            rank: rankedItem.rank,
            rankedValue: rankedItem,
        });

        return rankedItem.rank > -1;
    };
    return { fuzzyFilter };
};
