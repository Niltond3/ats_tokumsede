<script setup>
import { FormRegisterClient } from '@/components/forms/registerClient';
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { dialogState } from '@/hooks/useToggleDialog'

const emits = defineEmits(['update:dataTable'])

const { isOpen, toggleDialog } = dialogState()

const handleDialogOutsideInteract = (event) => {
    const classes = [];
    event.composedPath().forEach((element) => {
        if (element.classList) {
            classes.push(Array.from(element.classList));
        }
    });
    if (classes.join(" -").includes("pac-container")) event.preventDefault();
}

const handleSucess = () => {
    emits('update:dataTable')
    toggleDialog()
}

</script>

<template>
    <Dialog :open="isOpen" @update:open="(op) => toggleDialog()">
        <DialogTrigger as-child>
            <button class="text-info">
                <i class="ri-user-add-fill text-3xl"></i>
                <span class="hidden min-[426px]:block">Cliente</span>
            </button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[440px]" @interact-outside="handleDialogOutsideInteract">
            <DialogHeader>
                <DialogTitle class="text-info gap-1 flex items-center"><i class="ri-user-add-fill"></i>Cadastrar Cliente
                </DialogTitle>
                <DialogDescription class="text-xs text-start">
                    Após terminar todos os passos, clique em "Cadastrar" para concluir.
                </DialogDescription>
            </DialogHeader>
            <div
                class="grid gap-4 py-4 px-1 max-h-96 overflow-y-scroll text-xs scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2">
                <FormRegisterClient @create:success="handleSucess"></FormRegisterClient>
            </div>
        </DialogContent>
    </Dialog>
</template>
