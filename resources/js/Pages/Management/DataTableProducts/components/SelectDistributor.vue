<script setup>
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { utf8Decode } from '@/util';
// ['distributors', 'default']
const props = defineProps({
    distributors: {
        type: Array,
        required: true
    },
    default: {
        type: String,
        required: false
    }
})

const emit = defineEmits(['update:distributor'])

const handleSelectDistributor = (distributor) => emit('update:distributor', distributor)

</script>

<template>
    <Select @update:modelValue="handleSelectDistributor" :modelValue="props.default?.toString()">
        <SelectTrigger class="focus:!ring-transparent text-slate-500">
            <SelectValue placeholder="Distribuídoras" />
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectItem v-for="distributor in props.distributors" :key="distributor.id"
                    :value="`${distributor.id}`">
                    {{ utf8Decode(distributor.nome) }}
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
