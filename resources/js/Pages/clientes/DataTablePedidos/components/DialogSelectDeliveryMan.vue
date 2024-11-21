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
});

const emits = defineEmits(["on:deliveryManSelected"]);

const { isOpen, toggleDialog } = dialogState()

const handleDeliveryManSelected = (deliveryMan) => {
    emits('on:deliveryManSelected', deliveryMan)
    toggleDialog()
}

</script>

<template>
    <Dialog :open="isOpen" @update:open="(op) => toggleDialog()">
        <DialogTrigger as-child>
            <DropdownMenuItem class="cursor-pointer group gap-1" @select="(e) => e.preventDefault()">
                <i class="ri-e-bike-2-fill group-hover:text-dispatched transition-colors"></i>
                Despachar
            </DropdownMenuItem>
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
