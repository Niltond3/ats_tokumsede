<script setup>
import { useFormField } from "./useFormField";
import { cn } from "@/lib/utils";
import { Label } from "@/components/ui/label";

const props = defineProps({
    for: { type: String, required: false },
    asChild: { type: Boolean, required: false },
    as: { type: null, required: false },
    class: { type: null, required: false },
});

const { error, formItemId } = useFormField();
</script>

<template>
    <Label
        :class="cn(error && 'text-danger/65', 'absolute -top-4 text-info/50 peer-placeholder-shown:text-info text-[13px] px-1 left-px bg-white', props.class)"
        :for="formItemId">
        <slot />
    </Label>
</template>
