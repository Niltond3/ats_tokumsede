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
import { onMounted } from 'vue';

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

const props = defineProps({
    default: { type: String, required: false, default: '1' },
})

const emit = defineEmits(['update:paymentForm'])

const handlePaymentForm = (paymentForm) => emit('update:paymentForm', paymentForm)

onMounted(() => props)

</script>

<template>
    <Select @update:modelValue="handlePaymentForm" :modelValue="`${props.default}`">
        <SelectTrigger class="min-w-32 max-w-32 focus:!ring-transparent text-slate-500">
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
