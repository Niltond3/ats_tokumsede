import { twMerge } from "tailwind-merge";
import languagePtBR from "../../config/dataTablePtBR.mjs";
import { COLUMN_SIZES } from "@/constants/table";

export const tableConfig = (ajustClass, isNestedTable = false) => {
    const columns = [
        {
            data: "id",
            title: "#",
            width: COLUMN_SIZES.ID,
            responsivePriority: 7
        },
        {
            data: "distribuidor.nome",
            title: "Distribuidor",
            width: COLUMN_SIZES.DISTRIBUTOR,
            responsivePriority: 3,
            visible: !isNestedTable
        },
        {
            data: "produto.nome",
            title: "Produto",
            width: COLUMN_SIZES.PRODUCT,
            responsivePriority: 2
        },
        {
            data: "produto.componente",
            title: "Tipo",
            width: COLUMN_SIZES.TYPE,
            responsivePriority: 5,
            render: (data) => data ? 'Componente' : 'Produto'
        },
        {
            data: null,
            render: "#action",
            title: "Estoque",
            width: COLUMN_SIZES.ACTIONS,
            responsivePriority: 1
        },
        {
            data: "produto.status",
            render: "#status",
            title: "Status",
            width: COLUMN_SIZES.STATUS,
            responsivePriority: 4,
        }
    ];

    const options = {
        language: languagePtBR,
        processing: true,
        deferRender: true,
        orderClasses: false,
        dom: '<"top"lf>rt<"bottom"ip>', // Keeps elements within the table container
        responsive: {
            details: false,
            breakpoints: [
                { name: "bigdesktop", width: Infinity },
                { name: "meddesktop", width: 1480 },
                { name: "smalldesktop", width: 1280 },
                { name: "medium", width: 1188 },
                { name: "tabletl", width: 1024 },
                { name: "btwtabllandp", width: 848 },
                { name: "tabletp", width: 768 },
                { name: "mobilel", width: 480 },
                { name: "mobilep", width: 320 }
            ]
        },
        searchPanes: {
            cascadePanes: true
        },
        columnDefs: [{
            orderable: false,
            searchPanes: {
                show: true,
                controls: false,
                className: twMerge(
                    ajustClass,
                    "@container",
                    "[&>div.dtsp-topRow]:hidden",
                    "[&_.dt-layout-row]:!m-0",
                    "[&_.dt-scroll]:!m-0",
                    "[&_.dts_label]:hidden",
                    "[&_tr]:cursor-pointer",
                    "[&_td]:!pb-[14px] [&_td]:rounded-t-md",
                    "[&_tbody]:flex [&_tbody]:justify-center",
                    "[&_input]:opacity-0 [&_input]:pointer-events-none",
                    "[&_tr_div]:flex [&_tr_div]:gap-2 [&_tr_div]:transition-transform [&_tr_div]:text-[#1e88e5]/80",
                    "[&_tr.selected>td]:!shadow-[inset_0_0_0_9999px_rgba(30,136,229,1)] [&_tr.selected>td]:font-bold",
                    "[&_.dt-scroll-body]:!overflow-hidden [&_.dt-scroll-body]:!border-none",
                    "[&_tr>.dtsp-nameCont>span>div]:bg-success",
                    "[&_tr.selected_.dtsp-nameCont]:translate-y-[5px]",
                    "[&_.dt-scroll-head]:hidden",
                    "[&_tr.selected_div]:!text-white",
                    "[&_tr.selected_span.hidden]:!block",
                    "[&_.dt-scroll-body]:!h-[46px]",
                    "[&_table]:flex",
                    "[&_table>colgroup]:hidden",
                    "[&_table>thead]:hidden",
                    "absolute top-[55px] right-0",
                    "min-[768px]:top-[50px]",
                    "w-full",
                    "!overflow-hidden bg-transparent"
                )
            },
            targets: [1]
        }],
        layout: {
            top: "search",
            topStart: {
                searchPanes: {
                    layout: "columns-1",
                    cascadePanes: true
                }
            },
            topEnd: null
        }
    };

    return { columns, options };
};
