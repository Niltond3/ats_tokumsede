<script setup>
import {
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import DataTableProducts from '@/Pages/Management/DataTableProducts/components/DataTableProducts';

const props = defineProps({
    loadingDistributors: Boolean,
    distributors: Array,
    tableIdentifier: { type: String, required: false },
    distributorId: { type: [String, Number], required: false },
    globalFilter: { type: [String, null], required: true },
})


const emits = defineEmits(['update:distributor', 'update:globalFilter'])

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
        <div class="flex flex-col gap-2">
            <DialogDescription class="py-2 w-min text-nowrap">
                Cadastro dos preços padrões de cada distribuidor
            </DialogDescription>
            <div>
                <DataTableProducts.Header :distributors="distributors" :id-distribuidor="distributorId"
                    :loading-distributors="loadingDistributors" :global-filter="globalFilter"
                    :tableIdentifier="tableIdentifier" @update:distributor="handleUpdateDistributorSelect"
                    @update:global-filter="emits('update:globalFilter', $event)" />
            </div>
        </div>
    </DialogHeader>
</template>
