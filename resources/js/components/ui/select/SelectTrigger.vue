<script setup>
import { computed } from "vue";
import { SelectIcon, SelectTrigger, useForwardProps } from "radix-vue";
import { ChevronDown } from "lucide-vue-next";
import { cn } from "@/lib/utils";

const props = defineProps({
    disabled: { type: Boolean, required: false },
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
    <SelectTrigger v-bind="forwardedProps" :class="cn(
        'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-info/60 focus:outline-none focus:ring-2 focus:ring-offset-info/60 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:truncate text-start [&>span]:text-muted-foreground', 'transition-all duration-200 ease-linear motion-reduce:transition-none ',
        props.class,
    )
        ">
        <slot />
        <SelectIcon as-child>
            <ChevronDown class="w-4 h-4 opacity-50 shrink-0" />
        </SelectIcon>
    </SelectTrigger>
</template>
