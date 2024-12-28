<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    class: { type: null, required: false },
});
</script>

<template>
    <div :class="cn('animate-pulse rounded-md bg-slate-300', props.class)" />
</template>
