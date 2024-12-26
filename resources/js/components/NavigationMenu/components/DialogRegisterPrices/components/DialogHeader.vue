<script setup>
import {
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'
import { Skeleton } from '@/components/ui/skeleton'

defineProps({
    loadingDistributors: Boolean,
    distributors: Array
})

const emits = defineEmits(['update:distributor'])

const handleUpdateDistributorSelect = (distributorId) => {
    emits('update:distributor', distributorId)
}
</script>

<template>
    <DialogHeader>
        <DialogTitle class="leading-none flex gap-3 mr-4 text-lg text-info">
            <i class="ri-shopping-bag-3-fill"></i>
            <p class="font-semibold">Preços</p>
        </DialogTitle>
        <div class="flex gap-2">
            <DialogDescription class="py-2 w-min text-nowrap">
                Cadastro de preços
            </DialogDescription>
            <Skeleton v-if="loadingDistributors" class="w-full h-10" />
            <Select v-else @update:modelValue="handleUpdateDistributorSelect">
                <SelectTrigger>
                    <SelectValue placeholder="Selecione um distribuidor" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem v-for="distributor in distributors" :key="distributor.id"
                            :value="`${distributor.id}`">
                            {{ distributor.nome }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>
    </DialogHeader>
</template>
