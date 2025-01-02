import { h } from "vue";
import { utf8Decode, formatMoney } from "@/util";
import { toFloat } from "validator";
import { COLUMN_SIZES } from "./constants";
import { formatProductForClipboard } from "@/utils/clipboard";
import {
    SortableColumnHeader,
    DropDownOrderActions,
    DataTableNumberField,
    TableCell,
} from "../components";

const { toCurrency } = formatMoney();

export const imageColumn = {
    id: "img",
    accessorKey: "img",
    size: COLUMN_SIZES.IMG,
    header: () => h("div", { class: "text-white font-bold" }, "img"),
    cell: ({ row }) =>
        h("img", {
            class: "font-medium h-[85px] m-auto",
            src: `images/uploads/${row.getValue("img")}`,
        }),
    enableGlobalFilter: false,
};

export const nameColumn = {
    id: "nome",
    accessorKey: "nome",
    filterFn: "fuzzy",
    accessorFn: ({ nome }) => utf8Decode(nome),
    header: ({ column }) => {
        return h(SortableColumnHeader, {
            label: "nome",
            onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
        });
    },
    cell: ({ row }) => {
        return h(
            "div",
            {
                class: "flex gap-2 group cursor-pointer",
                onClick: () =>
                    navigator.clipboard.writeText(
                        formatProductForClipboard(row.original)
                    ),
            },
            utf8Decode(row.getValue("nome")),
            h("i", {
                class: "ri-file-copy-2-fill opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-info",
            })
        );
    },
    enableGlobalFilter: true,
};

export const priceColumn = {
    id: "preco",
    size: COLUMN_SIZES.PRICE,
    filterFn: "fuzzy",
    accessorFn: ({ preco }) => preco,
    header: ({ column }) => {
        return h(SortableColumnHeader, {
            label: "preço",
            onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
        });
    },
    cell: ({ row, getValue, column, table, cell }) => {
        const price = row.original;
        const rowIndex = row.index;
        const { meta } = table.options;
        const { tableData } = meta;
        const itens = meta.payload.itens;
        const getOffer = () => {
            if (tableData[rowIndex]?.precoEspecial) return true;
            return false;
        };
        const offer = getOffer();

        const payloadProduct = itens.filter(
            (produto) => produto.idProduto == price.id
        );

        const precoEspecial = price.precoEspecial;

        const getCellValue = (value) => {
            if (payloadProduct.length > 0) return payloadProduct[0].preco;
            return toFloat(`${value}`);
        };

        const getPriceComponent = (value, columnId) => {
            const cellValue = getCellValue(value);

            return h(
                TableCell,
                {
                    cellValue,
                    cellkey: cell.id,
                    offer,
                    onChanged: (val) =>
                        meta.updateData(row.index, columnId, val.value),
                },
                () => toCurrency(cellValue)
            );
        };

        if (precoEspecial) {
            const value = precoEspecial[precoEspecial.length - 1].val;
            return getPriceComponent(value, "precoEspecial");
        }
        const rowValue = getValue();
        const rowEndArray = rowValue.length - 1;
        const value = rowValue[rowEndArray].val;
        return getPriceComponent(value, column.id);
    },
    meta: {},
    enableGlobalFilter: true,
};

export const quantityColumn = {
    id: "quantidade",
    size: COLUMN_SIZES.QUANTITY,
    enableHiding: false,
    header: () => h("div", { class: "font-bold text-white" }, "quantidade"),
    cell: ({ row, column, table }) => {
        const payment = row.original;
        const itens = table.options.meta.payload.itens;

        const payloadProduct = itens.filter(
            (produto) => produto.idProduto == payment.id
        );

        const minQtd =
            itens.length > 0 &&
            itens
                .map((product) => product.quantidade)
                .reduce((curr, prev) => curr + prev) < 2
                ? 1
                : 0;

        return h(DataTableNumberField, {
            min: minQtd,
            value: payloadProduct.length > 0 ? payloadProduct[0].quantidade : 0,
            payment,
            onExpand: row.toggleExpanded,
            "onUpdate:modelValue": (val) => {
                table.options.meta.updateData(row.index, column.id, val);
            },
        });
    },
};

export const actionsColumn = {
    id: "actions",
    size: COLUMN_SIZES.ACTIONS,
    enableHiding: false,
    cell: ({ row, getValue, column, table, cell }) => {
        const payment = row.original;
        const preco = payment.preco;
        const precoEndArray = preco.length - 1;

        const { payload } = table.options.meta;
        const { clientId } = table.options.meta;

        const offer = {
            idProduto: payment.id,
            idDistribuidor: payload.idDistribuidor,
            idCliente: clientId,
            valor: preco[precoEndArray].val,
            qtdMin: preco[precoEndArray].qtd,
        };

        const pricePayload = {
            payload: offer,
            tableValue: [{ qtd: offer.qtdMin, val: offer.valor }],
        };
        return h(
            "div",
            { class: "relative" },
            h(DropDownOrderActions, {
                payment,
                offer,
                onChanged: (isOffer) => {
                    table.options.meta.updateData(
                        row.index,
                        "precoEspecial",
                        pricePayload
                    );
                },
            })
        );
    },
};