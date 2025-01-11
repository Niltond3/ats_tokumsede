// Move columns and options configurations to a separate config file
import { twMerge } from "tailwind-merge";
import languagePtBR from "./dataTablePtBR.mjs";

export const tableConfig = (ajustClass) => {
    const columns = [
        { data: "id", title: "#", responsivePriority: 7 },
        {
            data: "distribuidor.nome",
            title: "Distribuidor",
            responsivePriority: 3,
        },
        { data: "cliente.nome", title: "Cliente", responsivePriority: 2 },
        {
            data: "cliente.rating",
            render: "#rating",
            title: "Rating",
            responsivePriority: 6,
        },
        { data: "horarioPedido", title: "Data do Pedido" },
        { data: "dataAgendada", title: "Agendamento", responsivePriority: 4 },
        { data: "status.label", title: "status", responsivePriority: 5 },
        {
            data: "cliente.nome",
            render: "#action",
            title: "ações",
            responsivePriority: 1,
        },
        {
            data: "cliente.dddTelefone",
            title: "cliente.dddTelefone",
            visible: false,
        },
        { data: "cliente.telefone", title: "cliente.telefone", visible: false },
        {
            data: "endereco.logradouro",
            title: "endereco.logradouro",
            visible: false,
        },
        { data: "endereco.bairro", title: "endereco.bairro", visible: false },
        { data: "endereco.numero", title: "endereco.numero", visible: false },
        { data: "endereco.estado", title: "endereco.estado", visible: false },
        { data: "endereco.cidade", title: "endereco.cidade", visible: false },
    ];
    console.log('ajustClass')
    console.log(ajustClass)

    const options = {
        language: languagePtBR,
        processing: true,
        deferRender: true,
        orderClasses: false,
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
                { name: "mobilep", width: 320 },
            ],
        },
        searchPanes: {
            cascadePanes: true,
        },
        columnDefs: [
            {
                orderable: false,
                render: (data, type, row) => {
                    const state = (icon, peso) =>
                        `
                        <div class="flex items-center gap-1">
                            <i class="${icon} text-lg"></i>
                            <span class="w-0 opacity-0 pointer-events-none">${peso}</span>
                             <span class="hidden min-[768px]:block"> ${data} </span>
                        </div >
                     `;
                    const getType = {
                        Pendente: state("ri-error-warning-line", 1),
                        Aceito: state("ri-check-line", 2),
                        Despachado: state("ri-e-bike-2-line", 3),
                        Entregue: state("ri-check-double-line", 4),
                        Cancelado: state("ri-close-circle-line", 5),
                        Agendado: state("ri-calendar-schedule-line", 6),
                    };

                    const statusKey =
                        data == "Cancelado pelo Usuário" ||
                            data == "Não Localizado" ||
                            data == "Trote" ||
                            data == "Recusado"
                            ? "Cancelado"
                            : data;

                    return getType[statusKey];
                },
                searchPanes: {
                    show: true,
                    controls: false,
                    className: twMerge(
                        ajustClass,
                        "[&>div.dtsp-topRow]:hidden",
                        "[&_.dt-layout-row]:!m-0",
                        "[&_.dt-scroll]:!m-0",
                        "[&_.dts_label]:hidden",
                        "[&_tr]:cursor-pointer",
                        "[&_td]:!pb-[14px]  [&_td]:rounded-t-md",
                        "[&_tbody]:flex [&_tbody]:justify-center",
                        "[&_input]:opacity-0 [&_input]:pointer-events-none",
                        "[&_tr_div]:flex [&_tr_div]:gap-2 [&_tr_div]:transition-transform [&_tr_div]:text-[#1e88e5]/80",
                        "[&_tr.selected>td]:!shadow-[inset_0_0_0_9999px_rgba(30,136,229,1)] [&_tr.selected>td]:font-bold",
                        "[&_.dt-scroll-body]:!overflow-hidden [&_.dt-scroll-body]:!border-none",
                        "[&_tr>.dtsp-nameCont>span>div]:bg-success",
                        "[&_tr.selected_.dtsp-nameCont]:translate-y-[5px]",
                        "[&_.dt-scroll-head]:hidden",
                        "[&_tr.selected_div]:!text-white",
                        "[&_.dt-scroll-body]:!h-[46px]",
                        "[&_table]:flex",
                        "[&_table>colgroup]:hidden",
                        "[&_table>thead]:hidden",
                        "absolute top-[55px] right-0",
                        "min-[768px]:top-[50px]",
                        "w-full",
                        "!overflow-hidden bg-transparent"
                    ),
                },
                targets: [6],
            },
        ],
        layout: {
            top: "search",
            topStart: {
                searchPanes: {
                    layout: "columns-1",
                    cascadePanes: true,
                },
            },
            topEnd: null,
        },
        initComplete: function () {
            const api = this.api();
            const targetValue = 'Pendente';
            const tdQuerySelector = '.dtsp-searchPane.right-0 .dt-container .dt-layout-row .dt-layout-cell .dt-scroll .dt-scroll-body .dataTable tbody tr td'
            const rowNameQuerySelector = 'div span div .hidden'
            // Wait for the SearchPanes to be fully initialized
            const interval = setInterval(() => {
                const statusPaneItems = document.querySelectorAll(tdQuerySelector);

                if (statusPaneItems) {
                    // Find the target value to select
                    statusPaneItems.forEach(row => {
                        const rowName = row.querySelector(rowNameQuerySelector)
                        if (rowName) {
                            if (rowName.textContent.includes(targetValue)) {
                                row.click()
                                console.log("Preselected " + targetValue + " in the Status SearchPane.");
                            }
                            clearInterval(interval); // Stop checking once panes are ready
                            return
                        }
                    })
                    api.draw();
                } else {
                    console.error("Status SearchPane not found.");
                }
            }, 100); // Check every 100ms

        }
    };

    return { columns, options };
};
