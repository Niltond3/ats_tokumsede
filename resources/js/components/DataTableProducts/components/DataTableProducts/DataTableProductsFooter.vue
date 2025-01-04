<script setup>
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { Textarea } from '@/components/ui/textarea'
import SelectPayment from '@/components/orderComponents/SelectPayment.vue'
import ExchangeInput from '@/components/orderComponents/ExchangeInput.vue'
import { dateToISOFormat, formatMoney } from '@/util'
import { computed } from 'vue'

const props = defineProps({
    payload: Object,
    isUpdate: Boolean,
    disabledButton: Boolean,
    addressNote: String,
})

const { toCurrency } = formatMoney()

const emit = defineEmits(['update:paymentForm', 'update:exchange', 'update:scheduling', 'update:addressNote', 'callback:pedido'])

const values = computed(() => ([
    {
        label: 'Produtos',
        value: toCurrency(props.payload.totalProdutos)
    },
    {
        label: 'Entrega',
        value: toCurrency(props.payload.taxaEntrega)
    },
    {
        label: 'Total',
        value: toCurrency(props.payload.total)
    },
]))

</script>

<template>
    <div class="flex mt-3 gap-3 flex-col sm:grid sm:grid-cols-12 sm:grid-rows-2">
        <div class="flex items-center h-11 justify-around sm:col-span-4">
            <div class="w-full flex flex-col items-center justify-center">
                <Separator class="mt-1 mb-[0.35rem]" />
                <div class="flex gap-8">
                    <span v-for="value in values" class="text-sm font-medium relative text-info">
                        <p class="absolute -top-5 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">{{
                            value.label }}</p>
                        <p class="text-nowrap">{{ value.value }}</p>
                    </span>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 px-2 pb-2 sm:h-14 justify-center sm:row-start-2 sm:col-start-1 sm:col-span-10">
            <Separator label="Detalhes" class="z-100 my-1" />
            <SelectPayment @update:payment-form="$emit('update:paymentForm', $event)"
                :default="payload.formaPagamento.toString()" />
            <Separator orientation="vertical" class="" />
            <ExchangeInput @update:exchange="$emit('update:exchange', $event)" :value="payload.trocoPara" />
            <Separator orientation="vertical" class="hidden sm:block" />
            <DateTimePicker @update:scheduling="$emit('update:scheduling', $event)"
                :default:scheduling="dateToISOFormat(`${payload.dataAgendada} ${payload.horaInicio}`)" />
        </div>
        <div class="w-full relative flex gap-1 p-2 sm:col-start-5 sm:col-end-11">
            <Textarea
                class="border rounded-md border-gray-200 min-h-11 h-11 sm:min-h-16 focus-visible:ring-0 focus-visible:ring-offset-0"
                :model-value="addressNote" @update:model-value="$emit('update:addressNote', $event)" />
            <span class="absolute text-xs text-muted-foreground left-2 -top-0 bg-white">observação do endereço</span>
        </div>
        <Button :disabled="disabledButton" type="submit"
            class="sm:col-span-2 sm:col-end-13 sm:row-span-2 sm:my-3 border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/60 disabled:hover:bg-info/60 disabled:cursor-not-allowed"
            @click="$emit('callback:pedido')">
            <span v-if="isUpdate">Salvar</span>
            <span v-else>Cadastrar</span>
        </Button>
    </div>
</template>
