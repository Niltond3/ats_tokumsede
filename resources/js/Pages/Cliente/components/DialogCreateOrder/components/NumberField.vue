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

const props = defineProps({
  modelValue: { type: Number, required: true }, // Changed from 'value' to 'modelValue'
  min: { type: Number, required: true },
});

const value = ref(props.modelValue);
// Watch for changes in props.value and update the internal value ref
watch(
  () => props.modelValue,
  (newValue) => {
    value.value = newValue;
  },
);
// Watch for changes in the internal value ref and emit the update
watch(
  () => value.value,
  (newValue) => {
    emit('update:modelValue', newValue);
  },
);
</script>

<template>
  <NumberField id="quantidade" v-model="value" :default-value="0" :min="min">
    <NumberFieldContent>
      <NumberFieldDecrement />
      <NumberFieldInput />
      <NumberFieldIncrement />
    </NumberFieldContent>
  </NumberField>
</template>
