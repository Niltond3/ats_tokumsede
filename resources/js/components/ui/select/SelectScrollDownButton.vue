<script setup>
import { computed } from "vue";
import { SelectScrollDownButton, useForwardProps } from "radix-vue";
import { ChevronDown } from "lucide-vue-next";
import { cn } from "@/lib/utils";

const props = defineProps({
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
    <SelectScrollDownButton v-bind="forwardedProps" :class="cn('flex cursor-default items-center justify-center py-1 text-info', props.class)
        ">
        <slot>
            <ChevronDown class="h-4 w-4" />
        </slot>
    </SelectScrollDownButton>
</template>
