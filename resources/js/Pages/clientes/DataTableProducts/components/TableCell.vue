<script setup>
import { onMounted, ref } from 'vue';
import { formatMoney } from '@/util'


const props = defineProps({
    cellvalue: { type: String, required: false },
    cellkey: { type: String, required: false },
});

const { toCurrency, config } = formatMoney()
const inputElement = ref()
const initialValue = ref(toCurrency(parseFloat(props.cellvalue).toFixed(2)))
const showInput = ref(false)


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

        <div v-else class="relative items-center flex">
            <input type="text" ref="inputElement" @blur="handleBlur" @keypress.enter="handleBlur"
                v-model.lazy="initialValue"
                class="focus:ring-transparent focus:outline-none w-20 border border-gray-200 rounded-md p-1"
                v-money3="config" />
        </div>
    </div>
</template>

<script>
import { Money3Directive } from 'v-money3'
export default {
    directives: { money3: Money3Directive }
}
</script>
