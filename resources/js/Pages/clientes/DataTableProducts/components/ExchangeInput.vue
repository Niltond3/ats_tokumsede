<script setup>
import { ref, watch } from 'vue'
import { Input } from '@/components/ui/input'
import { formatMoney } from '@/util'
import { Money3Component } from 'v-money3'


const { config } = formatMoney()

const emits = defineEmits(['update:exchange']);


const props = defineProps(['value'])

const value = ref(parseFloat(props.value))

const handleBlur = (e) => emits('update:exchange', { value: e.target.value })


watch(() => props.value, (newValue) => {
    value.value = parseFloat(newValue)
})

</script>

<template>
    <div class="relative w-full max-w-sm items-center ">
        <Money3Component id="exchange" type="text" placeholder="Troco" class="focus-visible:!ring-transparent outline-none h-full rounded-md border border-input py-2 px-3 text-gray-500"
            v-bind="config" @keypress.enter="handleBlur" @blur="handleBlur" :model-value="value" />
    </div>
</template>
