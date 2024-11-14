<script setup>
import { markRaw, defineComponent, h } from 'vue';
import axios from 'axios';
import { Dialog } from '@/components/ui/dialog'
import { toast } from 'vue-sonner'
import DialogConfirmActionContent from '../../components/DialogConfirmActionContent.vue';

const props = defineProps({
    idAddress: { type: String, required: true },
    dialogTitle: { type: String, required: false },
    dialogDescription: { type: String, required: false },
    variant: { type: String, required: true },
    open: { type: Boolean, required: false },
});

const emits = defineEmits(["delete:confirm"]);

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})


const renderToast = (promise) => {
    toast.promise(promise, {
        loading: 'Aguarde...',

        success: (data) => {
            console.log(data)
            emits('delete:confirm', true)
            return markRaw(CustomDiv('sucesso', `O endereço foi deletado com sucesso!`));
        },
        error: (data) => {
            console.log(data)
            return markRaw(CustomDiv('Error', data.response))
        },
    });
}


const handleDeleteAddress = (confirm) => {
    if (confirm === false) return emits('delete:confirm', false)

    var url = `enderecos/${props.idAddress}`
    const response = axios.put(url, { status: 3 })
    renderToast(response)
}

</script>

<template>
    <Dialog :open="open" @update:open="(op) => toggleDialog()">
        <DialogConfirmActionContent v-bind="props" @on:confirm="handleDeleteAddress">
        </DialogConfirmActionContent>
    </Dialog>
</template>
