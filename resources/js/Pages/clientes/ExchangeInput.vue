<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { formatMoney } from '../useFormatMoney'
const [_, config] = formatMoney()

const emits = defineEmits(['update:exchange']);

const handleBlur = (e) => emits('update:exchange', { value: e.target.value })

</script>

<template>
    <div class="relative w-full max-w-sm items-center ">
        <Input id="exchange" type="text" placeholder="Troco" class="focus-visible:!ring-transparent" v-money3="config"
            @keypress.enter="handleBlur" @blur="handleBlur" />
    </div>
</template>
