<script setup lang="ts">
import { ref, watch } from 'vue';
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from '@/components/ui/number-field';

const emit = defineEmits(['update:modelValue']);

const props = defineProps(['value', 'min']);

const value = ref(props.value);

watch(
  () => value.value,
  (newValue) => {
    emit('update:modelValue', newValue);
  },
);
</script>

<template>
  <NumberField id="quantidade" :model-value="value" :default-value="0" :min="min">
    <NumberFieldContent>
      <NumberFieldDecrement />
      <NumberFieldInput />
      <NumberFieldIncrement />
    </NumberFieldContent>
  </NumberField>
</template>
