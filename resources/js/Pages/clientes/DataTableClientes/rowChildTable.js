import { utf8Decode } from '@/util'
import { twMerge } from 'tailwind-merge'

export default (d) => {

    const rowChildData = d

    console.log(rowChildData)
    //group-has-[li[aria-selected='true']]
    const containerClasses = `w-[49%] p-2`;
    const containerAddressClasses = twMerge(
        "hover:bg-info/5 transition-all max-h-[11rem] overflow-y-scroll overflow-x-hidden flex flex-col px-3 py-2bg-slate-100 ",
        "scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2 select");

    const liClasses = 'p-2 rounded hover:bg-info/80 transition-all text-info text-base flex gap-2 items-center group/line';
    const btClasses = 'w-[32px] h-[32px] text-2xl shadow-md rounded-full';
    const separator = 'w-px h-[32px] bg-slate-300'

    const customLiAddress = (endereco) => /*html*/`
<li class="${liClasses} deleteEndereco aria-selected:bg-dispatched aria-selected:my-2 transition-all duration-300 [transition-behavior:allow-discrete]" aria-selected="false"
    data-long-press-delay="500" id="${endereco.idCliente}" addr_id="${endereco.id}">
    <button
        class="${btClasses} editarEndereco relative text-info/60 transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg hover:text-info/100 group-aria-selected/line:hover:text-dispatched/100 group-aria-selected/line:text-dispatched/60"
        addr_id="${endereco.id}" cli_id="${endereco.idCliente}">
        <i
            class="ri-pencil-fill text-sm pointer-events-none opacity-0 absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 group-hover/line:opacity-100 group-aria-selected/line:!opacity-100"></i>
        <i
            class="ri-map-pin-fill group-hover/line:opacity-0 group-aria-selected/line:!opacity-0 transition-[colors,opacity] absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 "></i>
    </button>
    <div class="${separator}"></div>
    <span
        class="flex flex-col text-info group-hover/line:text-white group-aria-selected/line:!text-white pointer-events-none">
        ${utf8Decode(endereco.logradouro)}, nº ${endereco.numero} - ${utf8Decode(endereco.bairro)}
        <span
            class="text-xs text-info/50 group-hover/line:text-white group-aria-selected/line:!text-white/70">${utf8Decode(endereco.cidade)}
            -
            ${endereco.estado}</span>
        ${(
            () => {
                if (endereco.complemento || endereco.referencia) return /*html*/`
        <div
            class='overflow-hidden flex flex-col gap-2 mt-2 transition-max-height max-h-0 group-hover/line:max-h-40 group-aria-selected/line:!max-h-40 ease-in-out delay-150 pointer-events-none'>
            ${(
                        () => {
                            if (endereco.complemento) return /*html*/`
            <span class="font-bold text-sm text-white border-t border-slate-300">
                Complemento <span class="font-medium">${utf8Decode(endereco.complemento)}</span>
            </span>
            `
                            return ''
                        }
                    )()
                    }
            ${(
                        () => {
                            if (endereco.referencia) return /*html*/`
            <span class="font-bold text-sm text-white border-t border-slate-300">
                Referencia <span class="font-medium">${utf8Decode(endereco.referencia)}</span>
            </span>
            `
                            return ''
                        }
                    )()
                    }
        </div>
        `
                return ''
            }
        )()}
    </span>
    <div class="ml-auto border-l border-slate-300 pl-3">
        <button
            class="${btClasses} iniciarPedido hover:text-info/100 text-info/60 transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg group-aria-selected/line:text-dispatched/60 group-aria-selected/line:hover:text-dispatched/100"
            id="${endereco.id}"><i class="ri-shopping-cart-fill text-sm pointer-events-none"></i></button></div>
</li>
`;

    const orderDetails = (details) => {
        return details.map(detail => {
            const iconClasses = `${detail.classIcon} ${detail.classColor} group-hover/line:! group-aria-selected/line!:!text-white`
            return/*html*/`
            <div class="flex gap-1 items-center">
                <span class="min-w-[4.2rem] flex text-xs opacity-70 justify-start whitespace-nowrap">${detail.label.short}</span>
                <i class="${iconClasses} group-hover/line:text-white"></i>
                <span class="overflow-ellipsis whitespace-nowrap overflow-hidden w-[40%]">${detail.data}</span>
                ${(() => {
                    if (detail.author !== "" && detail.author != undefined) return /*html*/`<span class="text-xs opacity-70 justify-start">responsável</span><span class="whitespace-nowrap overflow-ellipsis overflow-hidden w-[40%]">${detail.author}</span></span>`
                    return ''
                })()}
                ${(() => {
                    if (detail.reason) return /*html*/`<span class="text-xs opacity-70 justify-start">motivo</span>${detail.reason}</span>`
                    return ''
                })()}
            </div>
        `
        }).join('')
    }
    const customLiOrder = (order) => /*html*/`
   <li class="${liClasses} hover:text-white">
    <i class="ri-e-bike-fill group-hover/line:text-white group-aria-selected/line:!text-white transition-colors"></i>
    <div class="${separator}"></div>
    <div class="flex flex-col gap-2 text-info group-hover/line:! group-aria-selected/line!:!text-white w-[82%] group-hover/line:text-white">
        ${orderDetails(order.details)}
        <div class="w-full h-px bg-slate-300"></div>
        <div
            class='overflow-hidden flex gap-2 mt-2 transition-max-height max-h-0 group-hover/line:max-h-40 group-aria-selected/line:!max-h-40 ease-in-out delay-150'>
            <div class="flex flex-col">
            <span> ${utf8Decode(order.endereco.logradouro || '')}, nº
                    ${order.endereco?.numero}
                    - ${utf8Decode(order.endereco.bairro || '')}
                    </span>${(() => {
            if (order.endereco.referencia) return /*html*/`<span>Referência: ${order.endereco.referencia}</span>`
            return ''
        })()}${(() => {
            if (order.endereco.complemento) return /*html*/`<span>Complemento:${order.endereco.complemento}</span>`
            return ''
        })()}
                <span class="text-xs opacity-60">
                    ${order.endereco.cidade} - ${order.endereco.estado}
                ${(() => {
            if (order.endereco.cep) return/*html*/`<span>,${order.endereco?.cep}</span>`
            return ''
        })()
        }
                </span>
                ${(() => {
            if (order.endereco.apelido) return /*html*/`<span
                    class="bg-info group-hover/line:bg-white group-aria-selected/line:!bg-white group-hover/line:text-info group-aria-selected/line:!text-info text-sm text-white w-min py-px px-2 rounded-full font-semibold">${order.endereco.apelido}</span>`
            return ''
        })()}
                </div>
        </div>
    </div>
    <div class="ml-auto border-l border-slate-300 pl-3">
        <button class="${btClasses} visualizarPedido hover:text-info/100 text-info/60 transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg flex justify-center items-center" id="${order.id}">
            <i class="ri-eye-fill text-sm pointer-events-none"></i>
        </button>
    </div>
    </li>`;

    return /*html*/`
<div class="flex pag-4 justify-between">
    <div class="${containerClasses} group">
        <div class="flex justify-between text-sm py-2 px-4 border-b border-slate-300 mb-1">
            <span class="text-info gap-2 text-base">
                <i class="ri-map-fill"></i>
                Endereços
            </span>

            <div class="flex gap-2">
                <button
                    class="gap-2 novoEndereco rounded-md text-white bg-info/80 hover:bg-info hover:shadow-md transition-all duration-300 transition-discrete font-semibold py-1 px-2 overflow-hidden flex
                    group-has-[li[aria-selected='true']]:justify-center
                    group-has-[li[aria-selected='true']]:items-center
                    group-has-[li[aria-selected='true']]:text-sm
                    group-has-[li[aria-selected='true']]:text-dispatched/60
                    group-has-[li[aria-selected='true']]:bg-white
                    group-has-[li[aria-selected='true']]:rounded-full
                    group-has-[li[aria-selected='true']]:p-0
                    group-has-[li[aria-selected='true']]:w-[32px]
                    group-has-[li[aria-selected='true']]:h-[32px]
                    group-has-[li[aria-selected='true']]:shadow-md
                    group-has-[li[aria-selected='true']]:hover:bg-dispatched
                    group-has-[li[aria-selected='true']]:hover:shadow-lg
                    group-has-[li[aria-selected='true']]:hover:text-white
                    "
                    id="${rowChildData.id}">
                    <i class="ri-map-pin-add-fill"></i>
                    <span class="pointer-events-none group-has-[li[aria-selected='true']]:hidden transition-discrete transition-[opacity, overlay, display]">Novo Endereço</span>
                </button>
                <div
                    class="relative transition-max-width max-w-0 group-has-[li[aria-selected='true']]:max-w-40 ease-in-out delay-150 overflow-x-hidden overflow-y-visible flex gap-2 group-has-[li[aria-selected='true']]:delay-75">
                    <button
                        class="${btClasses} copiarEndereco hover:text-white hover:bg-dispatched flex justify-center items-center text-dispatched/60 transition-all hover:shadow-lg">
                        <i class="ri-file-copy-2-fill text-sm pointer-events-none"></i>
                    </button>
                    <button
                        class="${btClasses} excluirEndereco hover:text-white hover:bg-dispatched flex justify-center items-center text-dispatched/60 transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg">
                        <i class="ri-delete-bin-2-fill text-sm pointer-events-none"></i>
                    </button>
                </div>
            </div>
        </div>
        <dl>
            <ul class="${containerAddressClasses}" id="enderecos">
                ${rowChildData.enderecos.map(endereco => customLiAddress(endereco)).join('')}
            </ul>
        </dl>
    </div>
    <div class="${containerClasses}">
        <div class="flex justify-between text-sm py-2 px-4 border-b border-slate-300 mb-1">
            <span class="text-info gap-2 text-base">
                <i class="ri-e-bike-2-fill"></i>
                Pedidos
            </span>
            <button
                class="gap-2 pointer-events-none opacity-0 rounded-md text-white bg-info/80 hover:bg-info hover:shadow-md transition-all font-semibold py-1 px-2">
                <i class="ri-map-pin-add-fill"></i>
            </button>
        </div>
        <dl>
            <ul class="${containerAddressClasses}" id="pedidos">
                ${rowChildData.pedidos.map(pedido => customLiOrder(pedido)).join('')}
            </ul>
        </dl>
    </div>
</div>
`
}
