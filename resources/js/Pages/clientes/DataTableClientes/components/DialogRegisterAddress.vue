<script setup>
import { FormRegisterClientAddress } from '@/components/forms/registerClient';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'


const props = defineProps({
    open: { type: Boolean, required: false },
    toggleDialog: { type: Function, required: false },
    idClient: { type: String, required: false },
});

const emits = defineEmits(['update:dataTable'])

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
    props.toggleDialog()
}

</script>

<template>
    <Dialog :open="open" @update:open="(op) => toggleDialog()">
        <DialogContent class="sm:max-w-[440px]" @interact-outside="handleDialogOutsideInteract">
            <DialogHeader>
                <DialogTitle class="text-info gap-1 flex items-center"><i class="ri-user-add-fill"></i>Cadastrar
                    Endereço
                </DialogTitle>
                <DialogDescription class="text-xs text-start">
                    Após terminar todos os passos, clique em "Cadastrar" para concluir.
                </DialogDescription>
            </DialogHeader>
            <div
                class="grid gap-4 py-4 px-1 max-h-96 overflow-y-scroll text-xs scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2">
                <FormRegisterClientAddress @cresate:success="handleSucess" :id-client="idClient" />
            </div>
        </DialogContent>
    </Dialog>
</template>
