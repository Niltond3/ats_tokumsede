<script setup>
import { cn } from '@/lib/utils';
import { TagsInputInput, useForwardProps } from 'radix-vue';
import { computed } from 'vue';

const props = defineProps({
    placeholder: { type: String, required: false },
    autoFocus: { type: Boolean, required: false },
    maxLength: { type: Number, required: false },
    asChild: { type: Boolean, required: false },
    as: { type: null, required: false },
    class: { type: null, required: false },
});

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <TagsInputInput v-bind="forwardedProps" :class="cn(
        'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600 border-input',
        props.class,
    )
        " />
</template>
