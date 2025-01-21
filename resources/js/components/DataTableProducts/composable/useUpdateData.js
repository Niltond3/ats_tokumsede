import { toast } from "vue-sonner";
import { formatMoney } from "@/util";
import renderToast from "@/components/renderPromiseToast";
import axios from "axios";

const createSpecialOffer = (payload, emit) => {
    const url = "preco";
    const promise = axios.post(url, payload);
    renderToast(
        promise,
        "Salvando oferta ...",
        "oferta salva com sucesso!",
        "Erro ao Salvar oferta!",
        () => emit("update:specialOfferCreated", true),
    );
};
export const useUpdateData = (tableProductsState, emit) => {
    const { toFloat } = formatMoney();

    const updateData = (rowIndex, columnId, value) => {
        const oldRow = tableProductsState.tableData[rowIndex];

        const updateTableData = (updateValue) => {
            console.log(updateValue)
            return tableProductsState.tableData.map((row, index) =>
                index === rowIndex
                    ? { ...oldRow, [columnId]: updateValue, updated: true }
                    : row
            )
        };

        const actions = {
            preco: () => {
                const endRowLength = oldRow[columnId].length - 1;
                return updateTableData([
                    {
                        precoId: oldRow[columnId][endRowLength].precoId,
                        qtd: oldRow[columnId][endRowLength].qtd,
                        val: toFloat(value),
                    },
                ]);
            },
            quantidade: () => updateTableData(value),
            precoEspecial: () => {
                if (value.payload) {
                    const { payload, tableValue } = value;
                    createSpecialOffer(payload, emit);
                    return updateTableData(tableValue);
                }
                const endRowLength = oldRow[columnId].length - 1;
                return updateTableData([
                    {
                        precoId: oldRow[columnId][endRowLength].precoId,
                        qtd: oldRow[columnId][endRowLength].qtd,
                        val: toFloat(value),
                    },
                ]);
            },
        };

        const newData = actions[columnId]
            ? actions[columnId]()
            : (toast.error("ação desconhecida"), tableProductsState.tableData);
        tableProductsState.tableData = newData;
    };

    return {
        updateData,
    };
};
