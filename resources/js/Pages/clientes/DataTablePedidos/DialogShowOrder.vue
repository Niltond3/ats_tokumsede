<script setup>
import { ref, onMounted } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogTrigger,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog'
import { Separator } from '@/components/ui/separator'
import { utf8Decode, formatMoney, dateToISOFormat } from '@/util';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { getStatusString } from './utils';
import { toast, Toaster } from 'vue-sonner'


const props = defineProps({
    orderId: { type: Number, required: true },
});

const data = ref([])

const { toCurrency } = formatMoney()

const phoneRegex = /^\s*(\d{2}|\d{0})[-. ]?(\d{5}|\d{4})[-. ]?(\d{4})[-. ]?\s*$/
const cepRegex = /(\d{5})-?(\d{3})/

onMounted(async () => {
    var url = `pedidos/visualizar/${props.orderId}`
    const response = await axios.get(url)

    const phone = `${response.data.cliente.dddTelefone}${response.data.cliente.telefone}`
    const postalCode = response.data.endereco?.cep

    const phoneMatch = phone.replace(/\D/g, '').match(phoneRegex)
    const cepMatch = postalCode.replace(/\D/g, '').match(cepRegex) || []

    const status = getStatusString(response.data.agendado, response.data.dataAgendada, response.data.horaInicio, response.data.status)
    const reason = response.data.retorno

    console.log(status)

    const details = [
        { classColor: '', classIcon: 'ri-calendar-fill', label: 'Horário Criação', data: response.data.horarioPedido, author: response.data.administrador },
        { classColor: status.classes.text, classIcon: status.classes.icon, label: 'Status', data: status.label, author: '', reason },
        { classColor: 'text-accepted', classIcon: 'ri-timer-fill', label: 'Horário Aceito', data: response.data.horarioAceito, author: response.data.aceitoPor },
        { classColor: 'text-dispatched', classIcon: 'ri-timer-fill', label: 'Horário Despachado', data: response.data.horarioDespache, author: response.data.despachadoPor },
        { classColor: 'text-info', classIcon: 'ri-timer-fill', label: 'Horário Entregue', data: response.data.horarioEntrega, author: response.data.entreguePor },
        { classColor: 'text-danger', classIcon: 'ri-timer-fill', label: 'Horário Cancelado', data: response.data.horarioCancelado, author: response.data.canceladoPor },
        { classColor: '', classIcon: 'ri-e-bike-fill', label: 'entregador', data: response.data.entregador?.nome || null, author: '' },
        { classColor: '', classIcon: 'ri-sticky-note-fill', label: 'Observação', data: response.data.obs || null, author: '' },
    ].filter(item => item.data)

    const itensPedido = response.data.itensPedido.map((order) => { return { ...order, preco: toCurrency(order.preco), subtotal: toCurrency(order.subtotal), produto: { ...order.produto, nome: utf8Decode(order.produto.nome) } } })

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

    const nome = utf8Decode(response.data.cliente.nome)
    const telefone = `(${phoneMatch[1]}) 9 ${phoneMatch[2]}-${phoneMatch[3]}`
    const total = toCurrency(response.data.total)
    const troco = toCurrency(response.data.troco)
    const trocoPara = toCurrency(response.data.trocoPara)
    const responseEndereco = response.data.endereco
    const cep = cepMatch.lenght > 1 ? `${cepMatch[1]}-${cepMatch[2]}` : null
    const formaPagamento = paymentFormToString[response.data.formaPagamento == 0 ? 1 : response.data.formaPagamento]
    const origem = originToString[response.data.origem]

    const endereco = {
        ...responseEndereco,
        cep,
        logradouro: utf8Decode(responseEndereco.logradouro || ''),
        bairro: utf8Decode(responseEndereco.bairro || ''),
        complemento: utf8Decode(responseEndereco.complemento || ''),
        referencia: utf8Decode(responseEndereco.referencia || ''),
        cidade: utf8Decode(responseEndereco.cidade || ''),
        apelido: utf8Decode(responseEndereco.apelido || ''),
    }

    data.value = {
        ...response.data,
        distribuidor: { ...response.data.distribuidor, nome: utf8Decode(response.data.distribuidor.nome) },
        cliente: { ...response.data.cliente, telefone, nome },
        endereco, status, details, itensPedido, total, troco, trocoPara, formaPagamento, origem
    }
    console.log(data.value)
})



function handleCopyOrder() {
    const { id: orderId, total, formaPagamento, troco, status: { label: statusLabel }, horarioPedido, horarioAceito, horarioEntrega, horarioDespache, horarioCancelado, dataAgendada, horaInicio, cliente: { nome: cliente, telefone }, distribuidor: { nome: distribuidorNome }, endereco: { logradouro, numero, bairro, complemento, cidade, estado, referencia }, itensPedido } = data.value

    const date = dateToISOFormat(horarioPedido)

    var minute = date.getMinutes().toString().padStart(2, '0');
    var hour = date.getHours().toString().padStart(2, '0');

    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };

    const formatDate = `${date.toLocaleDateString('pt-BR', options)} as HH:mm: ${hour}:${minute}`;

    const produtos = itensPedido.map((order) => `${order.qtd} ${order.produto.nome} un ${order.preco} subtotal ${order.subtotal}`).join('\n')

    navigator.clipboard.writeText(`
    #: ${orderId} | cliente: ${cliente}, Telefone: ${telefone}
    criado: ${formatDate}
    status: ${statusLabel}
    Endereço: ${logradouro}, nº ${numero} - ${complemento}, ${bairro} - ${cidade} ${estado} - ${referencia}
    distribuidor: ${distribuidorNome}
    ${produtos}`)
    toast.info('Copiado para a área de transferência', { position: 'top-center' })
}

</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <DropdownMenuItem class="cursor-pointer" @select="(e) => e.preventDefault()">
                <i class="ri-eye-fill"></i>
                Visualizar Pedido
            </DropdownMenuItem>
        </DialogTrigger>
        <DialogContent
            class="text-sm max-w-[22rem] sm:max-w-[30rem] md:max-w-[40rem] [&_div]:w-full [&_div]:flex [&_div]:flex-wrap gap-4">
            <DialogHeader>
                <DialogTitle class=" font-medium text-info leading-none flex gap-3 justify-between mr-4">
                    <button class="group" @click="handleCopyOrder">#{{ data.id }} <i
                            class="ri-file-copy-fill opacity-75 group-hover:opacity-100 transition-all"></i></button>
                    <span>{{ data.distribuidor.nome }}</span>
                </DialogTitle>
            </DialogHeader>
            <Separator label="cliente" />
            <div class="justify-between">
                <span><i class="ri-contacts-fill" /> {{ data.cliente.nome }}</span>
                <span><i class="ri-phone-fill" /> {{ data.cliente.telefone }}</span>
            </div>
            <Separator label="endereço de entrega" />
            <div class="flex-col">
                <span>{{ utf8Decode(data.endereco.logradouro || '') }}, {{ `nº ${data.endereco?.numero}` || 'SN' }} - {{
                    utf8Decode(data.endereco.bairro || '') }}</span>
                <span v-if="data.endereco.complemento">Complemento: {{ data.endereco.complemento }}</span>
                <span v-if="data.endereco.referencia">Referência: {{ data.endereco.referencia }}</span>
                <span class="text-xs opacity-60">{{ data.endereco.cidade }} - {{ data.endereco.estado
                    }} <span v-if="data.endereco.cep">, {{ data.endereco?.cep }}</span></span>

                <span v-if="data.endereco.apelido"
                    class="bg-info text-white w-min py-px px-2 rounded-full font-semibold">{{ data.endereco.apelido
                    }}</span>
            </div>
            <Separator label="outros detalhes" />
            <div class="flex-col relative gap-2 content-start">
                <span
                    class="relative bg-info text-white w-min flex-nowrap py-1 px-2 rounded-full font-semibold pointer-events-none">
                    {{ data.origem }}
                    <span
                        class="absolute left-1/2 -translate-x-1/2 z-10 -top-3 text-xs rounded-md bg-white text-info px-1">origem</span>
                </span>
                <div v-if="data.agendado" class=" flex gap-1">
                    <span class="w-[4.6rem] flex text-xs opacity-70 justify-start">
                        Horário Agendamento
                    </span>
                    <i class="ri-calendar-schedule-fill" />
                    {{ data.dataAgendada }}
                    <i class="ri-timer-fill" />
                    {{ data.horaInicio }}
                </div>

                <div class=" flex gap-1 items-center" v-for="detail in data.details">
                    <span class="w-[5.9rem] flex text-xs opacity-70 justify-start">
                        {{ detail.label }}
                    </span>
                    <i :class="[detail.classIcon, detail.classColor]" />
                    {{ detail.data }}
                    <span v-if='detail.author !== ""'> <span class="text-xs opacity-70 justify-start">responsável</span>
                        {{ detail.author }}</span>
                    <span v-if="detail.reason"> <span class="text-xs opacity-70 justify-start">motivo</span> {{
                        detail.reason }}</span>
                </div>
            </div>
            <Separator label="produtos " />
            <div v-for="order in data.itensPedido">
                <p>
                    {{ order.qtd }} {{ utf8Decode(order.produto.nome) }}
                    <span class="text-xs opacity-70 justify-start">un</span>
                    {{ toCurrency(order.preco) }}
                    <span class="text-xs opacity-70 justify-start">subtotal</span>
                    {{ toCurrency(order.subtotal) }}
                </p>
            </div>
            <p class="">
                <span class="text-xs opacity-70 justify-start">total</span>
                <span
                    class="relative bg-success text-white w-min flex-nowrap py-1 px-2 rounded-lg font-semibold pointer-events-none mx-2">
                    {{ data.total }}
                    <span
                        class="absolute left-1/2 -translate-x-1/2 z-10 -top-3 text-xs rounded-md bg-white text-success px-1">{{
                            data.formaPagamento }}</span>
                </span>
                <span v-if="data.trocoPara != 'R$ 0,00'">
                    <span class="text-xs opacity-70 justify-start">troco para {{ data.trocoPara }}
                        <i class="ri-arrow-left-right-line"></i>
                    </span>
                    {{ data.troco }}
                </span>
            </p>

        </DialogContent>
    </Dialog>
</template>
