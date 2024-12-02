<script setup>
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';

const props = defineProps({
    class: { type: null, required: false },
    defaultValue: { type: [String, Number], required: false },
    modelValue: { type: [String, Number], required: false },
});

const emits = defineEmits(['update:modelValue']);

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
</script>

<template>
    <textarea v-model="modelValue" :class="cn(
        'peer text-slate-600 focus-visible:ring-info/60  flex min-h-20 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-transparent placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50', 'transition-all duration-200 ease-linear motion-reduce:transition-none ', 'dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary',
        props.class,
    )
        " />
</template>
