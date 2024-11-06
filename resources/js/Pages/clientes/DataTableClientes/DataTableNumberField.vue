<script setup lang="ts">
import { ref, watch } from 'vue'
import { Label } from '@/components/ui/label'
import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from '@/components/ui/number-field'

const value = ref(0)

const emit = defineEmits(['update:modelValue'])

watch(() => value.value, (newValue) => {
    emit('update:modelValue', newValue)
})

</script>

<template>
    <NumberField id="quantidade" :default-value="0" :min="0" v-model:model-value="value">
        <NumberFieldContent>
            <NumberFieldDecrement />
            <NumberFieldInput />
            <NumberFieldIncrement />
        </NumberFieldContent>
    </NumberField>
</template>
