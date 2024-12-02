<script setup>
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { utf8Decode } from '@/util';
import { watch, ref } from 'vue';

const props = defineProps(['distributors', 'default'])

const defaultValue = ref(props.default)

const emit = defineEmits(['update:distributor'])

const handleSelectDistributor = (distributor) => emit('update:distributor', distributor)

watch(() => props.default, (newValue) => defaultValue.value = `${newValue}`)


</script>

<template>
    <Select @update:modelValue="handleSelectDistributor" :modelValue="defaultValue">
        <SelectTrigger class="focus:!ring-transparent text-slate-500">
            <SelectValue placeholder="Distribuídoras" />
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectLabel>Distribuídoras</SelectLabel>
                <SelectItem v-for="distributor in props.distributors" :key="distributor.id"
                    :value="`${distributor.id}`">
                    {{ utf8Decode(distributor.nome) }}
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
