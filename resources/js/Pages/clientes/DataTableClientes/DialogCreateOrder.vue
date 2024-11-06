<script setup>
import { ref, markRaw, defineComponent, h } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from "radix-vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { columns } from './Columns'
import DataTableProducts from './DataTableProducts.vue'
import { utf8Decode } from '@/util';
import { toast } from 'vue-sonner'

const props = defineProps({
    open: { type: Boolean, required: false },
    toggleDialog: { type: Function, required: false },
    idClienteAddress: { type: String, required: false },
    setTab: { type: Function, required: true },
});
const emits = defineEmits(["update:modelValue"]);

const forwarded = useForwardPropsEmits(props, emits);

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
            props.toggleDialog()
            props.setTab('pedidos')
            return markRaw(CustomDiv('sucesso', `O pedido foi cadastrado com sucesso!`));
        },
        error: (data) => markRaw(CustomDiv('Error', data.response)),
    });
}

const tableData = ref([])
const createOrderData = ref()
const whenDialogOpen = async () => {
    const url = `produtos/${props.idClienteAddress}`
    const response = await axios.get(url)
    console.log(response.data)

    const responseDistributor = response.data[1];
    const responseAddress = response.data[2];
    const address = {
        ...responseAddress,
        "logradouro": utf8Decode(responseAddress.logradouro),
        "bairro": utf8Decode(responseAddress.bairro),
        "complemento": utf8Decode(responseAddress.complemento || ''),
        "cidade": utf8Decode(responseAddress.cidade),
        "referencia": utf8Decode(responseAddress.referencia || ''),
        "apelido": utf8Decode(responseAddress.apelido || ''),
        "observacao": utf8Decode(responseAddress.observacao || ''),
    }

    const distributor = {
        ...responseDistributor,
        nome: utf8Decode(responseDistributor.nome),
    }

    createOrderData.value = {
        products: response.data[0],
        distributor,
        address,
        distributorExpedient: response.data[6],
        distributorTaxes: response.data[4],
    }
}

const handleDialogOpen = () => {
    props.open && whenDialogOpen()
    return props.open
}

const handleRealizarPedido = (payload) => {
    var url = "pedidos";
    const response = axios.post(url, payload)
    renderToast(response)
}
</script>

<template>
    <Dialog v-bind="forwarded" :open="handleDialogOpen()" @update:open="(op) => toggleDialog()">
        <DialogContent class="sm:max-w-3xl">
            <DataTableProducts :columns="columns" :table-data="tableData" :create-order-data="createOrderData"
                @callback:payload-pedido="handleRealizarPedido" />
        </DialogContent>
    </Dialog>
</template>
