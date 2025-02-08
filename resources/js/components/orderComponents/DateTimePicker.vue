<script setup>
import { ref, watch } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import { startOfTomorrow } from 'date-fns';
import '@vuepic/vue-datepicker/dist/main.css';
import { DateUtil } from '@/util';

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
    value: new Date(startOfTomorrow().setHours(8, 0, 0, 0)),
  },
]);

const format = (date) => {
  const { date: formattedDate, time } = DateUtil.dateToDayMonthYearFormat(date);
  return `${formattedDate}, ${time}`;
};

watch(
  () => props['default:scheduling'],
  (newValue) => {
    const newDate = newValue;
    date.value = newDate;
  },
);
const handleOpen = (e) => {
  console.log('event', e);
  console.log('datePicker open');
};

const handleClose = (e) => {
  console.log('event', e);
  console.log('datePicker close');
};
</script>

<template>
  <VueDatePicker
    v-model="date"
    :min-date="new Date()"
    locale="pt-BR"
    cancelText="Cancelar"
    selectText="Selecionar"
    :preset-dates="presetDates"
    :format="format"
    class="max-w-[200px] min-w-[200px] sm:order-1 sm:after:flex-[0_0_1]"
    time-picker-inline
    @open="handleOpen"
    @closed="handleClose"
    @update:modelValue="handleScheduling"
  >
    <template #preset-date-range-button="{ label, value, presetDate }">
      <span
        role="button"
        :tabindex="0"
        @click="presetDate(value)"
        @keyup.enter.prevent="presetDate(value)"
        @keyup.space.prevent="presetDate(value)"
      >
        {{ label }}
      </span>
    </template>
  </VueDatePicker>
</template>
