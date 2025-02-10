

import { StringUtil } from '@/util'



export default () => {
    const liStyles = 'p-2 rounded hover:bg-info/80 transition-all text-info text-base flex gap-2 items-center group/line';
    const buttonStyles = 'relative min-w-[32px] min-h-[32px] w-[32px] h-[32px] text-2xl shadow-md rounded-full transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg';
    const separatorStyles = 'w-px h-[32px] bg-slate-300'
    const typographyBaseStyles = 'text-sm text-info group-hover/line:text-white group-aria-selected/line:!text-white pointer-events-none select-none';
    const typographySmStyles = 'text-xs text-info/80 group-hover/line:!text-white/80 group-aria-selected/line:!text-white/80 pointer-events-none select-none';
    const iconStyles = 'text-sm pointer-events-none select-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transition-[colors,opacity]';

    const renderComplementAddress = (endereco) => {
        const renderComplement = (value) => {
            if (endereco[value]) return /*html*/`
                <span class="font-bold text-sm text-white border-t border-slate-300">
                    <span class="hidden">${value}</span>
                    <span class="font-medium">${StringUtil.utf8Decode(endereco[value])}</span>
                </span>
            `
            return ''
        }

        if (endereco.complemento || endereco.referencia) return /*html*/`
            <div class='overflow-hidden flex flex-col gap-2 mt-2 transition-max-height max-h-0 group-hover/line:max-h-40 group-aria-selected/line:!max-h-40 ease-in-out delay-150 pointer-events-none'>
                ${renderComplement('complemento')}
                ${renderComplement('referencia')}
            </div>
        `
        return ''
    }

    const renderAddress = (endereco) => {
        return /*html*/`<span
        class="flex flex-col ${typographyBaseStyles}">
        ${StringUtil.utf8Decode(endereco.logradouro)}, <span class="hidden">nº</span> ${endereco.numero}
        <span
            class="${typographySmStyles}">${StringUtil.utf8Decode(endereco.bairro)}</span>
        <span
            class="${typographySmStyles}">${StringUtil.utf8Decode(endereco.cidade)}
            -
            ${endereco.estado}</span>
            ${renderComplementAddress(endereco)}
    </span>`
    }

    const renderOrderDetails = (details) => {
        const renderOrderDetailConditional = (detail, conditional, label) => {
            const getAuthor = {
                'author': 'hidden',
                'default': ''
            }
            const isAuthor = getAuthor[conditional != 'author' ? 'default' : conditional]

            if (detail[conditional] !== "" && detail[conditional] != undefined) return /*html*/`<span class="text-xs opacity-70 justify-start ${isAuthor}">${label}</span><span class=" overflow-ellipsis overflow-hidden  ${isAuthor}">${detail[conditional]}</span></span>`
            return ''
        }
        return details.map(detail => {
            const iconClasses = `${detail.classIcon} ${detail.classColor}`
            return /*html*/`
            <div class="flex flex-col gap-2 items-start group-aria-selected/line:!text-white">
                <div class="flex gap-1 items-center">
                    <span class="text-xs opacity-70 min-w-[35px] max-w-[35px] min-[375px]:min-w-[70px] min-[375px]:max-w-[70px] truncate">${detail.label.short}</span>
                    <i class="${iconClasses} group-hover/line:text-white group-aria-selected/line:!text-white"></i>
                    <span class=" text-sm">${detail.data}</span>
                </div>
                ${renderOrderDetailConditional(detail, 'author', 'responsável')}
                ${renderOrderDetailConditional(detail, 'reason', 'motivo')}
            </div>
        `
        }).join('')
    }

    return {
        customLiAddress: (endereco, clientName) => {
            return /*html*/`<li
        class="${liStyles} deleteEndereco aria-selected:bg-dispatched aria-selected:my-2 transition-all duration-300 [transition-behavior:allow-discrete]"
        aria-selected="false" data-long-press-delay="500" id="${endereco.idCliente}" addr_id="${endereco.id}">
        <button
            class="${buttonStyles} editarEndereco text-info/60 hover:text-info/100 group-aria-selected/line:hover:text-dispatched/100 group-aria-selected/line:text-dispatched/60"
            addr_id="${endereco.id}" cli_id="${endereco.idCliente}">
            <i
                class="ri-pencil-fill ${iconStyles} opacity-0 group-hover/line:opacity-100 group-aria-selected/line:!opacity-100"></i>
            <i
                class="ri-map-pin-fill ${iconStyles} group-hover/line:opacity-0 group-aria-selected/line:!opacity-0 "></i>
        </button>
        <div class="${separatorStyles}"></div>
        ${renderAddress(endereco)}
        <div class="ml-auto border-l border-slate-300 pl-3">
            <button
                class="${buttonStyles} iniciarPedido hover:text-info/100 text-info/60 group-aria-selected/line:text-dispatched/60 group-aria-selected/line:hover:text-dispatched/100"
                id="${endereco.id}" data-client="${clientName}"><i class="ri-shopping-cart-fill ${iconStyles}"></i></button></div>
    </li>`;
        },
        customLiOrder: (order) => {

            return /*html*/`
            <li class="${liStyles} selectOrder hover:text-white aria-selected:bg-dispatched aria-selected:my-2 aria-selected:text-white transition-all duration-300 [transition-behavior:allow-discrete]"
            aria-selected="false" data-long-press-delay="500" client_id="${order.cliente.id}" order_id="${order.id}"
            >
            <button
            class="${buttonStyles} editOrder text-info/60 hover:text-info/100 group-aria-selected/line:hover:text-dispatched/100 group-aria-selected/line:text-dispatched/60"
            client_id="${order.cliente.id}" order_id="${order.id}">
            <i
                class="ri-pencil-fill ${iconStyles} opacity-0 group-hover/line:opacity-100 group-aria-selected/line:!opacity-100"></i>
            <i
                class="ri-truck-fill ${iconStyles} group-hover/line:opacity-0 group-aria-selected/line:!opacity-0 "></i>
        </button>
             <i class="ri-e-bike-fill group-hover/line:text-white group-aria-selected/line:!text-white transition-colors hidden select-none pointer-events-none"></i>
             <div class="${separatorStyles} hidden select-none pointer-events-none"></div>
             <div class="flex flex-col gap-2 text-info group-aria-selected/line!:!text-white group-hover/line:text-white select-none pointer-events-none">
                 ${renderOrderDetails(order.details)}
                 <div class="h-px bg-slate-300 select-none pointer-events-none"></div>
                 <div
                     class='overflow-hidden flex gap-2 mt-2 transition-max-height max-h-0 group-hover/line:max-h-40 group-aria-selected/line:!max-h-40 ease-in-out delay-150 select-none pointer-events-none'>
                     <div class="flex flex-col select-none pointer-events-none">
                        ${renderAddress(order.endereco)}
                         </div>
                 </div>
             </div>
             <div class="ml-auto border-l border-slate-300 pl-3">
                 <button class="${buttonStyles} visualizarPedido hover:text-info/100 text-info/60 transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg flex justify-center items-center" id="${order.id}">
                     <i class="ri-eye-fill text-sm pointer-events-none"></i>
                 </button>
             </div>
             </li>`
        },
        addressActionsButtons: (action, btClasses) => {
            return /*html*/`<button
            class="${btClasses} ${action.action} hover:text-white hover:bg-dispatched flex justify-center items-center text-dispatched/60 transition-all hover:shadow-lg">
            <i class="${action.icon} ${action.iconSize} pointer-events-none select-none"></i>
        </button>`
        }
    }

}
