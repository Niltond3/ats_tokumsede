<script setup>
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { MoreVertical } from 'lucide-vue-next'

defineProps({
    payment: {
        id: String
    }
})

function copy(id) {
    navigator.clipboard.writeText(id)
}
</script>

<template>
    <DropdownMenu>
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
            <DropdownMenuItem class="gap-1">
                <i class="ri-save-3-fill"></i>
                Salvar Oferta
            </DropdownMenuItem>
            <DropdownMenuItem>Visualizar</DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
