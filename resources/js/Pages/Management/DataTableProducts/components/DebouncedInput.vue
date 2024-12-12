<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
    debounce: {
        type: Number,
        default: 500,
    },
});

const emit = defineEmits(['update:modelValue']);

const timeout = ref();

const localValue = computed({
    get() {
        return props.modelValue;
    },
    set(newValue) {
        if (timeout.value) {
            clearTimeout(timeout.value);
        }
        timeout.value = setTimeout(
            () => emit('update:modelValue', newValue),
            props.debounce
        );
    },
});
onBeforeUnmount(() => clearTimeout(timeout.value));
</script>

<template>
    <input v-model="localValue" v-bind="$attrs"
        class="peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600 border-input" />
</template>
