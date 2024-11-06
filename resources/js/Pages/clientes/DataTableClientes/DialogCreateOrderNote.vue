<script setup>
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { dialogState } from '../useToggleDialog'

const [isOpen, toggleDialog] = dialogState()

const orderNote = ref('');

const emit = defineEmits(['callback:orderNote'])

const handleClick = () => {
    emit('callback:orderNote', orderNote.value)
    toggleDialog()
}

</script>

<template>
    <Dialog :open="isOpen" @update:open="() => toggleDialog()">
        <DialogTrigger as-child>
            <button
                class="absolute bg-dispatched p-0 rounded-md w-6 h-6 flex justify-center items-center shadow-lg transition-all hover:shadow-sm hover:rounded-full hover:w-8 hover:h-8 top-[-0.75rem] right-[-0.75rem] z-10">
                <i class="ri-sticky-note-add-fill text-white "></i>
            </button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader class="flex flex-row text-info items-center gap-2">
                <i class="ri-sticky-note-fill text-3xl"></i>
                <DialogTitle>Observações do Pedido</DialogTitle>
            </DialogHeader>
            <Textarea class="border rounded-md border-gray-200 focus-visible:ring-0 focus-visible:ring-offset-0"
                v-model:model-value="orderNote"></Textarea>
            <DialogFooter>
                <Button type="submit" @click="handleClick"
                    class="border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all">
                    Criar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
