<script setup>
import { cn } from "@/lib/utils";

const props = defineProps({
    class: { type: null, required: false, default: null },
});
</script>

<template>
    <div :class="cn(
        'rounded-lg border border-slate-200 bg-card text-card-foreground shadow-sm',
        props.class,
    )
        ">
        <slot />
    </div>
</template>
