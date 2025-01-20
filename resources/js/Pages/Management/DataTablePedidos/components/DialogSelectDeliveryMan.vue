<script setup>
import { useForwardPropsEmits } from "radix-vue";
import {
    Dialog,
    DialogContent,
    DialogTrigger,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog'
import { ScrollArea } from '@/components/ui/scroll-area'
import { Separator } from '@/components/ui/separator'
import { utf8Decode } from '@/util';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { dialogState } from '../../../../hooks/useToggleDialog'

const props = defineProps({
    entregadores: { type: Array, required: true },
    dropdown: { type: Boolean, required: false, default: true }
});

const emits = defineEmits(["on:deliveryManSelected", 'update:dialogOpen']);

const { isOpen, toggleDialog } = dialogState()

const handleDeliveryManSelected = (deliveryMan) => {
    emits('on:deliveryManSelected', deliveryMan)
    toggleDialog()
}

const handleDialogOpen = (op) => {
    !op && emits('update:dialogOpen', false)
    toggleDialog()
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="handleDialogOpen">
        <DialogTrigger as-child>
            <DropdownMenuItem v-if="dropdown" class="cursor-pointer group gap-1" @select="(e) => e.preventDefault()">
                <i class="ri-e-bike-2-fill group-hover:text-dispatched transition-colors"></i>
                <span class="hidden min-[426px]:block">Despachar</span>
            </DropdownMenuItem>
            <button v-else
                class="h-8 w-8 rounded-full bg-dispatched/80 focus:bg-dispatched text-white shadow-sm hover:shadow-md transition-all">
                <i class="ri-e-bike-2-fill group-hover:text-dispatched transition-colors"></i>
                <span class="sr-only">Despachar</span>
            </button>
        </DialogTrigger>
        <DialogContent class="gap-2 sm:max-w-min">
            <DialogHeader>
                <DialogTitle class=" font-medium text-info leading-none flex gap-3 justify-between mr-4">
                    <i class="ri-e-bike-2-line"></i>
                    Selecione o entregador
                </DialogTitle>
            </DialogHeader>
            <ScrollArea class="h-72 w-48 rounded-md p-3 flex flex-col">
                <button v-for="entregador in entregadores" :key="entregador.id"
                    class="w-full flex flex-col items-start text-sm text-slate-500 group-hover:text-blue-800 transition-all font-medium hover:translate-x-1 hover:text-blue-800 group"
                    @click="handleDeliveryManSelected(entregador.id)">
                    <div>
                        <i class="ri-e-bike-fill opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        {{ utf8Decode(entregador.nome) }}
                    </div>
                    <Separator class="my-2" />
                </button>
            </ScrollArea>
        </DialogContent>
    </Dialog>
</template>
