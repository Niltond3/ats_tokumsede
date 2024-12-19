<script setup>
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { MoreVertical } from 'lucide-vue-next'
import DialogConfirmAction from './DialogConfirAction.vue'

const props = defineProps({
    payment: {
        id: String
    },
    offer: { type: String }
})

const emit = defineEmits(['changed'])

const dropdownOpen = ref(false)

function copy(id) {
    navigator.clipboard.writeText(id)
}

const handleSaveOffer = (confirmSaveCallbak) => {
    const { reason } = confirmSaveCallbak

    if (!reason) return
    emit('changed', true)
}

const handleToggleDropdown = (op) => {
    if (op || op == false) dropdownOpen.value = !dropdownOpen.value
}
// :dialog-description=""
</script>

<template>
    <DropdownMenu :open="dropdownOpen" @update:open="handleToggleDropdown">
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="transition-colors text-cyan-700 p-0">
                <span class="sr-only">Abrir Menú</span>
                <MoreVertical class="w-6 h-6" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Ações</DropdownMenuLabel>
            <DropdownMenuItem class="gap-1" @click="copy(payment.id)">
                <i class="ri-file-copy-fill"></i>
                Copiar produto
            </DropdownMenuItem>
            <DropdownMenuItem class="gap-1">
                <i class="ri-pencil-fill"></i>
                Editar Produto
            </DropdownMenuItem>

            <DialogConfirmAction dialog-title="Salvar Oferta" trigger-icon="ri-save-3-fill"
                trigger-label="Salvar Oferta" variant="warning" @update:dialog-open="handleToggleDropdown"
                @on:confirm="handleSaveOffer" />
            <DropdownMenuItem>Visualizar</DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
