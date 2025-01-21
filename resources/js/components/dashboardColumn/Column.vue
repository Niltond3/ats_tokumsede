<script setup>
import { useForwardPropsEmits } from 'radix-vue';
import { variants } from '.';
import { cn } from '@/lib/utils';

const props = defineProps({
  variant: { type: null, required: false },
  class: { type: null, required: false },
});
const emits = defineEmits(['update:modelValue']);

const forwarded = useForwardPropsEmits(props, emits);

const { variant } = props;
const columnVariant = variants.column[variant || 'lg25'];
</script>

<template>
  <div
    v-bind="forwarded"
    class="relative w-full min-h-px px-4 bg-gray-100"
    :class="cn(columnVariant, props.class)"
  >
    <slot />
  </div>
</template>
