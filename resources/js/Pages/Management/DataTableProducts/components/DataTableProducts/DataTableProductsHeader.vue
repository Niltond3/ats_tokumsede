<script setup>
import { SelectDistributor } from '../'
import DebouncedInput from '../DebouncedInput.vue'

const props = defineProps({
    distributors: { type: Array, required: true },
    idDistribuidor: { type: null, required: true },
    tableIdentifier: { type: String, required: true },
    status: { type: Object, required: false },
    clientName: { type: String, required: false },
    globalFilter: { type: String, required: true },
})
const emits = defineEmits(['update:distributor', 'update:globalFilter'])

const handleDistributor = (value) => emits('update:distributor', value)
const handleUpdateDebouncedInput = (value) => emits('update:globalFilter', value)

</script>
<template>
    <div class="relative flex flex-wrap items-center pb-1 justify-between gap-3 group">
        <div class="flex flex-col gap-1 w-full md:flex-row">
            <DebouncedInput :modelValue="props.globalFilter" @update:modelValue="handleUpdateDebouncedInput"
                placeholder="Todos os produtos..." />
            <SelectDistributor v-if="props.distributors" :distributors="props.distributors"
                @update:distributor="handleDistributor" :default="`${props.idDistribuidor}`">
            </SelectDistributor>
            <span v-else class="font-medium flex items-center justify-center text-info py-1 px-2 w-full">
                {{ tableIdentifier }}
            </span>
        </div>
        <div class="flex flex-col gap-1 w-full md:flex-row pb-2">
            <button v-if="status"
                :class="[status.classes.bg, status.label == 'Agendado' ? 'text-slate-700' : 'text-white',]"
                class="relative font-semibold px-2 py-1 rounded-lg opacity-80 hover:opacity-100 "
                @click="handleStatusChange">
                <i v-if="status.label != 'Agendado' && status.label != 'Pendente'"
                    class="ri-edit-2-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
                    :class="status.classes.text"></i>
                <i v-if="status.oldStatus"
                    class="ri-arrow-go-back-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
                    :class="status.classes.text"></i>
                {{ status.label }}
            </button>
            <p class="text-sm font-semibold px-2 py-1 rounded-lg text-info ">
                Cliente:
                <span class="font-medium">
                    {{ clientName ?? '' }}
                </span>
            </p>
        </div>
    </div>
</template>
