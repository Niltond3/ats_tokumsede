<script setup>
import { ref } from 'vue';
import { Dialog, DialogTrigger } from '@/components/ui/dialog'
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { dialogState } from '../../../../hooks/useToggleDialog'
import DialogConfirmActionContent from '../../components/DialogConfirmActionContent.vue';

const props = defineProps({
    textReson: { type: Boolean, required: true },
    triggerLabel: { type: String, required: false },
    triggerIcon: { type: String, required: true },
    dialogTitle: { type: String, required: false },
    dialogDescription: { type: String, required: false },
    variant: { type: String, required: true }
})

const { isOpen, toggleDialog } = dialogState()

const emits = defineEmits(["on:confirm"]);

const reson = ref('');

const handleConfirm = (confirm) => {
    if (confirm === false) return toggleDialog()
    emits('on:confirm', reson.value == '' ? true : reson.value)
}

const getVariant = {
    danger: {
        textClasses: {
            icon: 'ri-close-circle-fill',
            text: 'text-danger group-hover:text-danger'
        },
        bgClasses: 'bg-danger/70 hover:bg-danger'
    },
    warning: {
        textClasses: {
            icon: 'ri-error-warning-fill',
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
                <i :class="[props.triggerIcon, styleVariant.textClasses.text]" class="transition-colors"></i>
                {{ props.triggerLabel }}
            </DropdownMenuItem>
        </DialogTrigger>
        <DialogConfirmActionContent v-bind="props" @on:confirm="handleConfirm" />
    </Dialog>
</template>
