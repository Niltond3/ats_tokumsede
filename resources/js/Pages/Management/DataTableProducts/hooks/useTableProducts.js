import { rankItem } from "@tanstack/match-sorter-utils";
export const tableProdutcs = () => {
    const fuzzyFilter = (row, columnId, value, addMeta) => {
        // Handle global filter
        if (columnId === "globalFilter") {
            const searchableColumns = ["nome", "preco"];
            return searchableColumns.some((col) => {
                const rowValue = row.getValue(col);
                const itemValue =
                    col === "preco"
                        ? rowValue[0].val.toString()
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

        console.log(rowValue);
        console.log(columnId);
        console.log(value);

        const itemValue =
            columnId === "preco"
                ? rowValue[0].val.toString()
                : rowValue.toString();

        console.log(itemValue);

        const rankedItem = rankItem(itemValue, value, {
            threshold: 0.2,
            keepDiacritics: true,
        });

        console.log(rankedItem);

        addMeta({
            rank: rankedItem.rank,
            rankedValue: rankedItem,
        });

        return rankedItem.rank > -1;
    };
    return { fuzzyFilter };
};
