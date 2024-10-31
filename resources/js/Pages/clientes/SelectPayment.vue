<script setup>
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'

const paymentForms = [{
    label: 'Dinheiro',
    value: '1'
}, {
    label: 'Cartão',
    value: '2'
}, {
    label: 'Pix',
    value: '3'
}, {
    label: 'Transferência',
    value: '4'
}
]

const emit = defineEmits(['update:paymentForm'])

const handlePaymentForm = (paymentForm) => emit('update:paymentForm', paymentForm)

</script>

<template>
    <Select @update:modelValue="handlePaymentForm">
        <SelectTrigger class="w-[180px] focus:!ring-transparent">
            <SelectValue placeholder="Forma de pagamento" />
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectLabel>Forma de pagamento</SelectLabel>
                <SelectItem v-for="paymentForm in paymentForms" :key="paymentForm.value" :value="paymentForm.value">
                    {{ paymentForm.label }}
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
