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
import { utf8Decode, formatMoney } from '@/util';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { getStatusString } from './utils';

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

    const telefone = `(${phoneMatch[1]}) 9 ${phoneMatch[2]}-${phoneMatch[3]}`
    const cep = cepMatch.lenght > 1 ? `${cepMatch[1]}-${cepMatch[2]}` : null
    const status = getStatusString(response.data.agendado, response.data.dataAgendada, response.data.status)
    const reason = response.data.retorno

    console.log(response.data)

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



    data.value = {
        ...response.data, cliente: { ...response.data.cliente, telefone }, endereco: { ...response.data.endereco, cep }, status, details
    }

    console.log(data.value)
})

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
                    <span>#{{ data.id }}</span>
                    <span>{{ utf8Decode(data.distribuidor.nome || '') }}</span>
                </DialogTitle>
            </DialogHeader>
            <Separator label="cliente" />
            <div class="justify-between">
                <span><i class="ri-contacts-fill" /> {{ utf8Decode(data.cliente.nome) }}</span>
                <span><i class="ri-phone-fill" /> {{ data.cliente.telefone }}</span>
            </div>
            <Separator label="endereço de entrega" />
            <div class="flex-col">
                <span>{{ utf8Decode(data.endereco.logradouro || '') }}, {{ `nº ${data.endereco?.numero}` || 'SN' }} - {{
                    utf8Decode(data.endereco.bairro || '') }}</span>
                <span v-if="data.endereco.complemento">Complemento: {{ utf8Decode(data.endereco.complemento || '')
                    }}</span>
                <span v-if="data.endereco.referencia">Referência: {{ utf8Decode(data.endereco.referencia || '')
                    }}</span>
                <span class="text-xs opacity-60">{{ utf8Decode(data.endereco.cidade || '') }} - {{ data.endereco.estado
                    }} <span v-if="data.endereco.cep">, {{ data.endereco?.cep }}</span></span>

                <span v-if="data.endereco.apelido"
                    class="bg-info text-white w-min py-px px-2 rounded-full font-semibold">{{
                        utf8Decode(data.endereco.apelido || '')
                    }}</span>
            </div>
            <Separator label="outros detalhes" />
            <div class="flex-col relative gap-2 content-start">
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
                <p>{{ order.qtd }} {{ utf8Decode(order.produto.nome) }}, un {{ toCurrency(order.preco) }} = {{
                    toCurrency(order.subtotal)
                    }}</p>
            </div>
        </DialogContent>
    </Dialog>
</template>
