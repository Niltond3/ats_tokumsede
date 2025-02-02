<script setup>
import { useVModel } from '@vueuse/core';
import { cn } from '@/lib/utils';

const props = defineProps({
  defaultValue: { type: [String, Number], required: false },
  modelValue: { type: [String, Number], required: false },
  class: { type: null, required: false },
});

const emits = defineEmits(['update:modelValue']);

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
});
</script>

<template>
  <input
    v-model="modelValue"
    :class="
      cn(
        'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600 border-input',
        props.class,
      )
    "
  />
</template>
