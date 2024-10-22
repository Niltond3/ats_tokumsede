<script setup>
import { useForwardPropsEmits } from "radix-vue";
import { variants } from '.';
import { cn } from "@/lib/utils";


const props = defineProps({
    percentual: { type: String, required: true },
    variant: { type: null, required: false },
    class: { type: null, required: false },
});
const emits = defineEmits(["update:modelValue"]);

const forwarded = useForwardPropsEmits(props, emits);

const { variant } = props
const barVariant = variants.ProgressBar[variant || 'info']
</script>

<template>
    <div v-bind="forwarded" class="h-auto flex overflow-hidden text-xs bg-gray-100 rounded">
        <div class="flex flex-col text-white text-center whitespace-nowrap transition-width "
            :class="cn(barVariant, props.class)" role="progressbar" :style="{ width: percentual + '%', height: '6px' }"
            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</template>
