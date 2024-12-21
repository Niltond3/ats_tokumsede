import { h, ref } from "vue";
import { ArrowUpDown } from "lucide-vue-next";
import { utf8Decode } from "@/util";
import { Button } from "@/components/ui/button";
import { createColumnHelper } from "@tanstack/vue-table";
import {
    DropDownOrderActions,
    DataTableNumberField,
    TableCell,
} from "../components";
import { formatMoney } from "@/util";
import { toFloat } from "validator";

const { toCurrency } = formatMoney();

const columnHelper = createColumnHelper();

export const columns = [
    {
        id: "img",
        accessorKey: "img",
        size: 44,
        header: () => h("div", { class: "text-white font-bold" }, "img"),
        cell: ({ row }) =>
            h("img", {
                class: "font-medium h-[85px] m-auto",
                src: `images/uploads/${row.getValue("img")}`,
            }),
        enableGlobalFilter: false,
    },
    {
        id: "nome",
        accessorKey: "nome",
        filterFn: "fuzzy",
        accessorFn: ({ nome }) => utf8Decode(nome),
        header: ({ column }) => {
            return h(
                Button,
                {
                    class: "hover:!bg-transparent ring-transparent p-0 hover:!text-white text-sm font-bold text-white",
                    variant: "ghost",
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === "asc"),
                },
                () => [
                    "nome",
                    h(ArrowUpDown, {
                        class: "hover:!bg-transparent ring-transparent p-0 hover:!text-white ml-2 h-4 w-4",
                    }),
                ]
            );
        },
        cell: ({ row }) =>
            h("div", { class: "" }, utf8Decode(row.getValue("nome"))),
        enableGlobalFilter: true,
    },
    {
        id: "preco",
        accessorKey: "preco",
        filterFn: "fuzzy",
        size: 75,
        accessorFn: ({ preco }) => preco,
        header: ({ column }) => {
            return h(
                Button,
                {
                    class: "hover:!bg-transparent ring-transparent p-0 hover:!text-white text-sm font-medium font-bold text-white",
                    variant: "ghost",
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === "asc"),
                },
                () => [
                    "preco",
                    h(ArrowUpDown, {
                        class: "hover:!bg-transparent ring-transparent p-0 hover:!text-white ml-2 h-4 w-4",
                    }),
                ]
            );
        },
        cell: ({ row, getValue, column, table, cell }) => {
            const price = row.original;
            const rowIndex = row.index;
            const itens = table.options.meta.payload.itens;
            const { tableData } = table.options.meta;
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
                        onChanged: (val) => {
                            table.options.meta.updateData(
                                row.index,
                                columnId,
                                val.value
                            );
                        },
                    },
                    () => toCurrency(cellValue)
                );
            };

            if (precoEspecial) {
                const value = precoEspecial[precoEspecial.length - 1].val;
                return getPriceComponent(value, "precoEspecial");
            }
            const value = getValue()[0].val;
            return getPriceComponent(value, column.id);
        },
        meta: {},
        enableGlobalFilter: true,
    },
    {
        id: "quantidade",
        size: 90,
        enableHiding: false,
        header: () => h("div", { class: "font-bold text-white" }, "quantidade"),
        cell: ({ row, getValue, column, table, cell }) => {
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
                value:
                    payloadProduct.length > 0
                        ? payloadProduct[0].quantidade
                        : 0,
                payment,
                onExpand: row.toggleExpanded,
                "onUpdate:modelValue": (val) => {
                    table.options.meta.updateData(row.index, column.id, val);
                },
            });
        },
    },
    {
        id: "actions",
        size: 38,
        enableHiding: false,
        cell: ({ row, getValue, column, table, cell }) => {
            const payment = row.original;
            const { payload } = table.options.meta;
            const { clientId } = table.options.meta;

            const offer = {
                idProduto: payment.id,
                idDistribuidor: payload.idDistribuidor,
                idCliente: clientId,
                valor: payment.preco[0].val,
                qtdMin: payment.preco[0].qtd,
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
    },
];
