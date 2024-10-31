<script setup>
import { cn } from '@/lib/utils';
import { NumberFieldRoot, useForwardPropsEmits } from 'radix-vue';
import { computed, onMounted, ref, watch } from 'vue';
import EditCell from './EditCell.vue';
import { formatMoney } from '../useFormatMoney'
//getValue, row, column, table
const props = defineProps({
    cellvalue: { type: String, required: false },
    cellkey: { type: String, required: false },
});

const [toCurrency, config] = formatMoney()
const inputElement = ref()
const initialValue = ref(toCurrency(props.cellvalue))
const showInput = ref(false)

watch(() => props.cellValue, (newValue) => {
    console.log(newValue)
    initialValue.value = newValue
})

const emits = defineEmits(['changed']);

function handleClick() {
    showInput.value = true
    setTimeout(() => {
        (inputElement.value).focus()
    }, 200)
}

function handleBlur() {
    showInput.value = false
    emits('changed', { key: props.cellkey, value: initialValue.value })
}


</script>

<template>
    <div @click="handleClick">
        <div v-if="!showInput">
            <slot />
        </div>

        <input v-else type="text" ref="inputElement" @blur="handleBlur" @keypress.enter="handleBlur"
            v-model="initialValue"
            class="focus:ring-transparent focus:outline-none w-20 border border-gray-200 rounded-md p-1"
            v-money3="config">
    </div>
</template>
