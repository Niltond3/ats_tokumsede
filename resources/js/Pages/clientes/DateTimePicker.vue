<template>
    <VueDatePicker @update:modelValue="handleScheduling" v-model="date" locale="pt-BR" cancelText="Cancelar"
        selectText="Selecionar" :preset-dates="presetDates">
        <template #preset-date-range-button="{ label, value, presetDate }">
            <span role="button" :tabindex="0" @click="presetDate(value)" @keyup.enter.prevent="presetDate(value)"
                @keyup.space.prevent="presetDate(value)">
                {{ label }}
            </span>
        </template>
    </VueDatePicker>
</template>

<script setup>
import { ref } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import { startOfTomorrow } from 'date-fns';
import '@vuepic/vue-datepicker/dist/main.css'

const date = ref();

const emit = defineEmits(['update:scheduling'])

const handleScheduling = (paymentForm) => emit('update:scheduling', paymentForm)

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
</script>
