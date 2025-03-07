import { StringUtil } from "@/util";
import { twMerge } from "tailwind-merge";
import components from "./components";

export default (d) => {
    const { customLiAddress, customLiOrder, addressActionsButtons } = components();

    const rowChildData = d;
    const clientName = StringUtil.utf8Decode(rowChildData.nome);
    const containerClasses = `p-2 md:flex-1`;
    const addressActions = [
        {
            action: 'copiarEndereco',
            icon: 'ri-file-copy-2-fill',
            iconSize: 'text-sm',
            clientId: rowChildData.id
        },
        {
            action: 'excluirEndereco',
            icon: 'ri-delete-bin-2-fill',
            iconSize: 'text-sm',
            clientId: rowChildData.id
        },
        {
            action: 'novoPrecoEspecial',
            icon: 'ri-price-tag-3-fill',
            iconSize: 'text-sm',
            clientId: rowChildData.id
        },
        {
            action: 'atualizarCoordenadas',
            icon: 'ri-refresh-fill',
            iconSize: '!text-md',
            clientId: rowChildData.id
        },
    ]
    const containerAddressClasses = twMerge(
        "md:!max-h-[11rem]",
        "hover:bg-info/5  flex flex-col px-3 py-2bg-slate-100 ",
        "max-h-0 !overflow-y-scroll !overflow-x-hidden transition-max-height delay-300 ease-in-out [transition-behavior:allow-discrete]",
        "scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2 select"
    );

    const btClasses =
        "min-w-8 min-h-8 size-8 text-base shadow-md rounded-full ";

    return /*html*/ `
<div class="flex flex-col md:flex-row justify-between w-full">
    <div class="${containerClasses} group">
        <div class="flex justify-between text-sm py-2 px-4 border-b border-slate-300 mb-1 items-center h-[50px]">
            <button class="accordionController text-white gap-2 text-base bg-info/80 hover:bg-info hover:shadow-md transition-all font-semibold py-1 px-2 rounded-md md:after:!hidden after:content-['+'] after:text-slate-100 after:font-bold after:ml-1 after:w-4 after:h-4 after:inline-block after:text-center flex">
                <i class="ri-map-fill pointer-events-none select-none"></i>
                <span class="group-has-[li[aria-selected='true']]:hidden min-[405px]:group-has-[li[aria-selected='true']]:block overflow-hidden transition-all duration-1000 transition-discrete pointer-events-none select-none">Endereços</span>
            </button>

            <div class="flex gap-2">
                <button
                    class="gap-2 novoEndereco text-white bg-info/80 hover:bg-info shadow-m hover:shadow-lg transition-all duration-300 transition-discrete font-semibold py-1 px-2 overflow-hidden flex
                    justify-center items-center text-sm rounded-full p-0 w-[32px] h-[32px]
                    group-has-[li[aria-selected='true']]:justify-center
                    group-has-[li[aria-selected='true']]:items-center
                    group-has-[li[aria-selected='true']]:text-dispatched/60
                    group-has-[li[aria-selected='true']]:bg-white
                    group-has-[li[aria-selected='true']]:rounded-full
                    group-has-[li[aria-selected='true']]:p-0
                    group-has-[li[aria-selected='true']]:w-[32px]
                    group-has-[li[aria-selected='true']]:h-[32px]
                    group-has-[li[aria-selected='true']]:hover:bg-dispatched
                    group-has-[li[aria-selected='true']]:hover:text-white
                    "
                    id="${rowChildData.id}">
                    <i class="ri-map-pin-add-fill"></i>
                    <span class="pointer-events-none select-none group-has-[li[aria-selected='true']]:hidden transition-discrete transition-[opacity, overlay, display] hidden">Novo Endereço</span>
                </button>
                <div
                    class="relative transition-max-width max-w-0 group-has-[li[aria-selected='true']]:max-w-40 ease-in-out delay-150 overflow-x-hidden overflow-y-visible flex gap-2 group-has-[li[aria-selected='true']]:delay-75">
                    ${addressActions.map((action) => addressActionsButtons(action, btClasses)).join("")}
                </div>
            </div>
        </div>
        <dl>
            <ul class="${containerAddressClasses}" id="enderecos">
                ${rowChildData.enderecos
            .map((endereco) => customLiAddress(endereco, clientName))
            .join("")}
            </ul>
        </dl>
    </div>
    <div class="${containerClasses}">
        <div class="flex justify-between text-sm py-2 px-4 border-b border-slate-300 mb-1 items-center h-[50px]">
            <button class="accordionController text-white gap-2 text-base bg-info/80 hover:bg-info hover:shadow-md transition-all font-semibold py-1 px-2 rounded-md md:after:!hidden after:content-['+'] after:text-slate-100 after:font-bold after:ml-1 after:w-4 after:h-4 after:inline-block after:text-center">
                <i class="ri-e-bike-2-fill pointer-events-none font-light select-none"></i>
                <span class="group-has-[li[aria-selected='true']]:hidden pointer-events-none select-none">Pedidos</span>
            </button>
        </div>
        <dl>
            <ul class="${containerAddressClasses}" id="pedidos">
                ${rowChildData.pedidos
            .map((pedido) => customLiOrder(pedido))
            .join("")}
            </ul>
        </dl>
    </div>
</div>
`;
};
