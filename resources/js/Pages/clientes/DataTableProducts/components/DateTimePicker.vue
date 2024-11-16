<script setup>
import { ref, watch } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import { startOfTomorrow } from 'date-fns';
import '@vuepic/vue-datepicker/dist/main.css'
import { dateToDayMonthYearFormat } from '@/util';

const props = defineProps(['default:scheduling']);

const date = ref(props['default:scheduling']);

const emit = defineEmits(['update:scheduling']);

const handleScheduling = (paymentForm) => emit('update:scheduling', paymentForm);

const presetDates = ref([
    {
        label: 'hoje',
        value: new Date(),
    },
    {
        label: 'Amanhã',
        value: startOfTomorrow(new Date()),
    },
]);

const format = (date) => {
    const { date: formattedDate, time } = dateToDayMonthYearFormat(date)
    return `${formattedDate}, ${time}`
}


watch(() => props['default:scheduling'], (newValue) => {
    const newDate = newValue
    date.value = newDate
})

</script>

<template>
    <VueDatePicker @update:modelValue="handleScheduling" :min-date="new Date()" v-model="date" locale="pt-BR"
        cancelText="Cancelar" selectText="Selecionar" :preset-dates="presetDates" :format="format"
        class="max-w-[200px] min-w-[200px] sm:order-1 sm:after:flex-[0_0_1]">
        <template #preset-date-range-button="{ label, value, presetDate }">
            <span role="button" :tabindex="0" @click="presetDate(value)" @keyup.enter.prevent="presetDate(value)"
                @keyup.space.prevent="presetDate(value)">
                {{ label }}
            </span>
        </template>
    </VueDatePicker>
</template>
