<script setup>
import { ref, onMounted, watch } from 'vue';
import {
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog'
import { Separator } from '@/components/ui/separator'
import { utf8Decode, formatMoney } from '@/util';
import { formatOrder, handleCopyOrder } from '../utils';

const props = defineProps({
    orderId: { type: Number, required: true },
    isOpen: { type: Boolean, required: false },
});

const data = ref({})

const { toCurrency } = formatMoney()

const fetchOrder = async () => {
    var url = `pedidos/visualizar/${props.orderId}`
    const response = await axios.get(url)
    const formatedOrder = formatOrder(response.data)

    const itensPedido = response.data.itensPedido.map((order) => { return { ...order, preco: toCurrency(order.preco), subtotal: toCurrency(order.subtotal), produto: { ...order.produto, nome: utf8Decode(order.produto.nome) } } })

    data.value = { ...formatedOrder, itensPedido }
}

watch(() => props.isOpen, async (newValue) => fetchOrder())

onMounted(async () => {
    if (props.orderId) fetchOrder()
})

</script>

<template>
    <DialogContent
        class="text-sm max-w-[22rem] sm:max-w-[30rem] md:max-w-[40rem] [&_div]:w-full [&_div]:flex [&_div]:flex-wrap gap-4 max-h-[560px] overflow-scroll scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2">
        <DialogHeader>
            <DialogTitle class=" font-medium text-info leading-none flex gap-3 justify-between mr-4">
                <button class="group" @click="handleCopyOrder">#{{ data.id }} <i
                        class="ri-file-copy-fill opacity-75 group-hover:opacity-100 transition-all"></i></button>
                <span>{{ data.distribuidor?.nome }}</span>
            </DialogTitle>
            <DialogDescription>Detalhes do pedido</DialogDescription>
        </DialogHeader>
        <Separator label="cliente" />
        <div class="justify-between">
            <span><i class="ri-contacts-fill" /> {{ data.cliente?.nome }}</span>
            <span><i class="ri-phone-fill" /> {{ data.cliente?.telefone }}</span>
        </div>
        <Separator label="endereço de entrega" />
        <div class="flex-col">
            <span>{{ data.endereco?.logradouro }}, {{ `nº ${data.endereco?.numero}` || 'SN' }} - {{
                data.endereco?.bairro }}</span>
            <span v-if="data.endereco?.complemento">Complemento: {{ data.endereco.complemento }}</span>
            <span v-if="data.endereco?.referencia">Referência: {{ data.endereco.referencia }}</span>
            <span class="text-xs opacity-60">{{ data.endereco?.cidade }} - {{ data.endereco?.estado
                }} <span v-if="data.endereco?.cep">, {{ data.endereco?.cep }}</span></span>

            <span v-if="data.endereco?.apelido"
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
            <div v-if="data.agendado" class=" flex gap-1 items-center">
                <span class="w-[5.9rem] flex text-xs opacity-70 justify-start">
                    Horário Agendamento
                </span>
                <i class="ri-calendar-schedule-fill" />
                {{ data.dataAgendada }}
                <i class="ri-timer-fill" />
                {{ data.horaInicio }}
            </div>

            <div class=" flex gap-1 items-center" v-for="detail in data.details">
                <span class="w-[5.9rem] flex text-xs opacity-70 justify-start">
                    {{ detail.label.long }}
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
</template>
