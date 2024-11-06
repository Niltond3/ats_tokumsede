<script setup>
import { ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogTrigger,
    DialogHeader,
    DialogTitle,
    DialogDescription
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { dialogState } from './useToggleDialog'

const props = defineProps({
    textReson: { type: Boolean, required: true },
    triggerLabel: { type: String, required: false },
    triggerIcon: { type: String, required: true },
    dialogTitle: { type: String, required: false },
    dialogDescription: { type: String, required: false },
    variant:{type: String, required:true}
})

const [isOpen, toggleDialog] = dialogState()

const emits = defineEmits(["on:confirm"]);

const reson = ref('');

const handleConfirm = () => {
    emits('on:confirm', reson.value == '' ? true : reson.value)
    toggleDialog()
}

const getVariant = {
    danger: {
        textClasses: {
            icon:'ri-close-circle-fill',
            text: 'text-danger group-hover:text-danger'
        },
        bgClasses: 'bg-danger/70 hover:bg-danger'
    },
    warning: {
        textClasses: {
            icon:'ri-error-warning-fill',
            text: 'text-warning group-hover:text-warning'
        },
        bgClasses: 'bg-warning/70 hover:bg-warning'
    }
}

const styleVariant = getVariant[props.variant]

</script>

<template>
    <Dialog :open="isOpen" @update:open="(op) => toggleDialog()">
        <DialogTrigger as-child>
            <DropdownMenuItem class="cursor-pointer group gap-1" @select="(e) => e.preventDefault()">
                <i :class="[props.triggerIcon, styleVariant.textClasses.text]"
                    class="transition-colors"></i>
                {{ props.triggerLabel }}
            </DropdownMenuItem>
        </DialogTrigger>
        <DialogContent class="gap-2">
            <DialogHeader>
                <DialogTitle :class="[styleVariant.textClasses.text]" class="leading-none flex gap-3 mr-4 text-lg">
                    <i :class="[styleVariant.textClasses.icon]"></i>
                    <p class="font-semibold">{{ props.dialogTitle }}</p>
                </DialogTitle>
                <DialogDescription>
                    {{ props.dialogDescription }}
                </DialogDescription>
            </DialogHeader>

            <div v-if="textReson || false">
                <Textarea id="message-2" class="!ring-0 !ring-transparent" placeholder="Digite o motivo."
                    v-model="reson" />
                <p class="text-sm text-muted-foreground">
                    Justifique o motivo do cancelamento deste pedido
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline"
                    class="rounded-md py-1 px-4 bg-info/70 hover:bg-info transition-all !text-white"
                    @click="toggleDialog()">Desistir</Button>
                <Button variant="outline"
                    :class="[styleVariant.bgClasses]"
                    class="rounded-md py-1 px-4 transition-all !text-white"
                    @click="handleConfirm()">Confirmar</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
