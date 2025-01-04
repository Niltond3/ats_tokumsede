<script setup>
import { ref, watch } from 'vue'
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
import { dialogState } from '@/hooks/useToggleDialog'
import { twMerge } from 'tailwind-merge'

const { isOpen, toggleDialog } = dialogState()


const emit = defineEmits(['callback:order-note'])

const props = defineProps(['orderNote', 'class'])

const note = ref(props.orderNote);


const handleClick = () => {
    emit('callback:order-note', note.value)
    toggleDialog()
}

watch(() => props.orderNote, (newValue) => {
    note.value = newValue
})


</script>

<template>
    <Dialog :open="isOpen" @update:open="() => toggleDialog()">
        <DialogTrigger as-child>
            <button
                :class="twMerge('absolute bg-dispatched p-0 rounded-md w-6 h-6 flex justify-center items-center shadow-lg transition-all hover:shadow-sm hover:rounded-full hover:w-8 hover:h-8 top-[-0.75rem] right-[-0.75rem] z-10 border-2 border-white', props.class)">
                <i class="ri-sticky-note-add-fill text-white "></i>
            </button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[440px] flex flex-col gap-2">
            <DialogHeader class="text-info ">
                <div class="flex flex-row items-center gap-2">
                    <i class="ri-sticky-note-fill text-3xl" />
                    <DialogTitle>Observações</DialogTitle>
                </div>
                <DialogDescription>Adicione uma observação para o pedido</DialogDescription>
            </DialogHeader>
            <Textarea class="border rounded-md border-gray-200 focus-visible:ring-0 focus-visible:ring-offset-0"
                v-model:model-value="note">
            </Textarea>
            <DialogFooter>
                <Button type="submit" @click="handleClick"
                    class="border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all">
                    Criar
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
