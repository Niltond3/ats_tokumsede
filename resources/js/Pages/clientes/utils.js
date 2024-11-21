import { ref } from 'vue'
import { dateToISOFormat, utf8Decode, formatMoney } from "@/util";
import { toast } from 'vue-sonner'

export const getStatusString = (agendado, dataAgendada, horaInicio, status) => {

    const dateIso = dateToISOFormat(`${dataAgendada} ${horaInicio}`)
    const currentDate = new Date();
    const scheduleDate = new Date(dateIso);


    const statusKey = ((agendado == 1) && (currentDate < scheduleDate)) ? 9 : status

    const statusString = {
        1: {
            label: 'Pendente',
            classes: {
                bg: 'bg-warning',
                text: 'text-warning',
                icon: 'ri-error-warning-fill'
            }
        },
        2: {
            label: 'Cancelado pelo Usuário',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        3: {
            label: 'Não Localizado',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        4: {
            label: 'Trote',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        5: {
            label: 'Recusado',
            classes: {
                bg: 'bg-danger',
                text: 'text-danger',
                icon: 'ri-close-circle-fill'
            }
        },
        6: {
            label: 'Despachado',
            classes: {
                bg: 'bg-dispatched',
                text: 'text-dispatched',
                icon: 'ri-e-bike-2-fill'
            }
        },
        7: {
            label: 'Entregue',
            classes: {
                bg: 'bg-info',
                text: 'text-info',
                icon: 'ri-check-double-fill'
            }
        },
        8: {
            label: 'Aceito',
            classes: {
                bg: 'bg-accepted',
                text: 'text-accepted',
                icon: 'ri-check-double-fill'
            }
        },
        9: {
            label: 'Agendado',
            classes: {
                bg: 'bg-muted',
                text: 'text-gray-400',
                icon: 'ri-calendar-schedule-fill'
            }
        }
    }

    const statusId = {
        'Pendente': 1,
        'Cancelado pelo Usuário': 2,
        'Não Localizado': 3,
        'Trote': 4,
        'Recusado': 5,
        'Despachado': 6,
        'Entregue': 7,
        'Aceito': 8,
    }
    return statusString[statusKey]
}


export const formatOrder = (order) => {
    const { toCurrency } = formatMoney()

    const phoneRegex = /^\s*(\d{2}|\d{0})[-. ]?(\d{5}|\d{4})[-. ]?(\d{4})[-. ]?\s*$/
    const cepRegex = /(\d{5})-?(\d{3})/

    const phone = `${order.cliente.dddTelefone}${order.cliente.telefone}`
    const postalCode = order.endereco?.cep

    const phoneMatch = phone.replace(/\D/g, '').match(phoneRegex)
    const cepMatch = postalCode.replace(/\D/g, '').match(cepRegex) || []

    const statusId = order.status
    const status = getStatusString(order.agendado, order.dataAgendada, order.horaInicio, order.status)
    const reason = order.retorno

    const label = {
        short: '',
        long: ''
    }

    const details = [
        { classColor: '', classIcon: 'ri-calendar-fill', label: { short: 'Criado', long: 'Horário Criado' }, data: order.horarioPedido, author: order.administrador },
        { classColor: status.classes.text, classIcon: status.classes.icon, label: { short: 'Status', long: 'Status' }, data: status.label, author: '', reason },
        { classColor: 'text-accepted', classIcon: 'ri-timer-fill', label: { short: 'Aceito', long: 'Horário Aceito' }, data: order.horarioAceito, author: order.aceitoPor },
        { classColor: 'text-dispatched', classIcon: 'ri-timer-fill', label: { short: 'Despachado', long: 'Horário Despachado' }, data: order.horarioDespache, author: order.despachadoPor },
        { classColor: 'text-info', classIcon: 'ri-timer-fill', label: { short: 'Entregue', long: 'Horário Entregue' }, data: order.horarioEntrega, author: order.entreguePor },
        { classColor: 'text-danger', classIcon: 'ri-timer-fill', label: { short: 'Cancelado', long: 'Horário Cancelado' }, data: order.horarioCancelado, author: order.canceladoPor },
        { classColor: '', classIcon: 'ri-e-bike-fill', label: { short: 'entregador', long: 'entregador' }, data: utf8Decode(order.entregador?.nome || ''), author: '' },
        { classColor: '', classIcon: 'ri-sticky-note-fill', label: { short: 'Observação', long: 'Observação' }, data: utf8Decode(order.obs || ''), author: '' },
    ].filter(item => item.data)

    const paymentFormToString = {
        1: 'Dinheiro',
        2: 'Cartão',
        3: 'Pix',
        4: 'Transferência',
        5: 'Ifood',
    }

    const originToString = {
        1: 'App Android',
        2: 'App IOS',
        3: 'Plataforma',
        4: 'Auto atendimento Web'
    }

    const nome = utf8Decode(order.cliente.nome)
    const telefone = `(${phoneMatch[1]}) 9 ${phoneMatch[2]}-${phoneMatch[3]}`
    const total = toCurrency(order.total)
    const troco = toCurrency(order.troco)
    const trocoPara = toCurrency(order.trocoPara)
    const responseEndereco = order.endereco
    const cep = cepMatch.lenght > 1 ? `${cepMatch[1]}-${cepMatch[2]}` : null
    const formaPagamento = paymentFormToString[order.formaPagamento == 0 ? 1 : order.formaPagamento]
    const origem = originToString[order.origem]

    const endereco = {
        ...responseEndereco,
        cep,
        logradouro: utf8Decode(responseEndereco.logradouro || ''),
        bairro: utf8Decode(responseEndereco.bairro || ''),
        complemento: utf8Decode(responseEndereco.complemento || ''),
        referencia: utf8Decode(responseEndereco.referencia || ''),
        cidade: utf8Decode(responseEndereco.cidade || ''),
        apelido: utf8Decode(responseEndereco.apelido || ''),
        observacao: utf8Decode(responseEndereco.observacao || ''),
        cliente: { ...responseEndereco.cliente, nome, telefone },
    }

    return {
        ...order,
        distribuidor: { ...order.distribuidor, nome: utf8Decode(order.distribuidor.nome) },
        cliente: { ...order.cliente, telefone, nome },
        endereco, status, statusId, details, total, troco, trocoPara, formaPagamento, origem
    }
}

export function orderToClipboard(order) {
    const { id: orderId, total, formaPagamento, troco, status: { label: statusLabel }, horarioPedido, horarioAceito, horarioEntrega, horarioDespache, horarioCancelado, dataAgendada, horaInicio, cliente: { nome: cliente, telefone }, distribuidor: { nome: distribuidorNome }, endereco: { logradouro, numero, bairro, complemento, cidade, estado, referencia }, itensPedido, obs, trocoPara } = order

    console.log(order)

    const date = dateToISOFormat(horarioPedido)

    var minute = date.getMinutes().toString().padStart(2, '0');
    var hour = date.getHours().toString().padStart(2, '0');

    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };

    const formatDate = `${date.toLocaleDateString('pt-BR', options)} as HH:mm: ${hour}:${minute}`;

    const produtos = itensPedido.map((order) => `${order.qtd} ${order.produto.nome} un ${order.preco} subtotal ${order.subtotal}`).join('\n')

    navigator.clipboard.writeText(`
    ---------- Pedido nº ${orderId} ----------
    cliente: ${cliente}, Telefone: ${telefone}
    criado: ${formatDate}
    status: ${statusLabel} ${dataAgendada ? `- para ${dataAgendada} às ${horaInicio}` : ''}
    Endereço: ${logradouro}, nº ${numero} - ${complemento}, ${bairro} - ${cidade} ${estado} - ${referencia}
    distribuidor: ${distribuidorNome}
    forma de pagamento: ${formaPagamento} ${trocoPara !== "R$ 0,00" ? `- troco: ${troco}` : ''}
    ${produtos}
    total: ${total}
    ${obs ? `obs: ${obs}` : ''}
    ---------------------------------------`)
    toast.info('Copiado para a área de transferência', { position: 'top-center' })
}

export function getClientFormat() {
    const getSexo = {
        mobile: {
            1: 'Masculino',
            2: 'Feminino',
        },
        desktop: {
            1: 'M',
            2: 'F',
        }
    }

    const getTipoPessoaPayload = (documentValue) => {
        if (!documentValue) return {
            tipoPessoa: '1',
            documento: {
                'CPF': null,
                'CNPJ': null

            }
        }
        if (documentValue.length <= 14) {
            return {
                tipoPessoa: '1',
                documento: {
                    'CPF': documentValue.replace(/[^a-zA-Z0-9]/g, ''),
                    'CNPJ': null

                }
            }
        }

        return {
            tipoPessoa: '2',
            documento: {
                'CPF': null,
                'CNPJ': documentValue.replace(/[^a-zA-Z0-9]/g, '')
            }
        }

    }

    return { getSexo, getTipoPessoaPayload };
}

export const useGoogleAutocomplete = (inputId) => {

    const place = ref()

    const options = {
        componentRestrictions: {
            country: 'br'
        },
        strictBounds: true
    };


    const autocomplete = new google.maps.places.Autocomplete(document.getElementById(inputId), options);


    autocomplete.setFields(['place_id', 'geometry', 'address_component', 'formatted_address']);

    const infowindow = new google.maps.InfoWindow();

    autocomplete.addListener('place_changed', function () {
        infowindow.close();

        place.value = autocomplete.getPlace();

    });

    setTimeout(() => (document.body.style.pointerEvents = ""), 0)

    return { place }
}