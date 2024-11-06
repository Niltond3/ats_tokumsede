import { utf8Decode } from '@/util'
export default (d) => {
    const containerClasses = ``;
    const containerAddressClasses = "max-h-[11rem] overflow-y-scroll overflow-x-hidden flex flex-col px-3 py-2bg-slate-100 ";
    const liClasses = 'p-2 rounded hover:bg-slate-200 transition-all text-base flex gap-2 items-center group';
    const btClasses = 'w-6 h-6 text-2xl';
    return    /*html*/`
    <div class="${containerClasses}">
    <dl>
        <ul class="${containerAddressClasses}">
            ${d.enderecos.map(endereco => {
        console.log(endereco)
        return /*html*/`
                <li class="${liClasses}">
                    <span class="w-3/5 flex flex-col text-gray-600">
                        ${utf8Decode(endereco.logradouro)}, nº ${endereco.numero} - ${utf8Decode(endereco.bairro)}
                        <span class="text-xs text-gray-400">${utf8Decode(endereco.cidade)} - ${endereco.estado}</span>
                        ${(
                () => {
                    if (endereco.complemento || endereco.referencia) return /*html*/`
                                <div class='overflow-hidden flex flex-col gap-2 mt-2 transition-max-height max-h-0 group-hover:max-h-40 ease-in-out delay-150'>
                                    ${(
                            () => {
                                if (endereco.complemento) return /*html*/`
                                                <span class="font-bold text-sm text-gray-500 border-t border-slate-300">
                                                    Complemento <span class="font-medium">${endereco.complemento}</span>
                                                </span>
                                                `
                                return ''
                            }
                        )()
                        }
                                    ${(
                            () => {
                                if (endereco.referencia) return /*html*/`
                                                                                                                                                                        <span class="font-bold text-sm text-gray-500 border-t border-slate-300">
                                                    Referencia <span class="font-medium">${endereco.referencia}</span>
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
                     <div class="gap-3 flex">
                        <button class="${btClasses} editarEndereco" id="${endereco.id}"><i class="ri-pencil-fill"></i></button>
                        <button class="${btClasses} excluirEndereco text-danger" id="${endereco.id}"><i class="ri-delete-bin-2-fill"></i></button>
                        <button class="${btClasses} iniciarPedido hover:text-cyan-600 text-cyan-800 transition-colors" id="${endereco.id}"><i class="ri-shopping-cart-fill pointer-events-none"></i></button>
                    </div>
                </li>
                `
    }).join('')}
        </ul>
    </dl>
    </div>
    `
}
