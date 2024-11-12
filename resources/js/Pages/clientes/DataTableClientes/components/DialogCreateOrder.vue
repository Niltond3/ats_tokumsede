<script setup>
import { ref, markRaw, defineComponent, h } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from "radix-vue";
import {
    Dialog,
    DialogContent,
} from '@/components/ui/dialog'
import { DataTableProducts } from '../../DataTableProducts/'
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

const createOrderData = ref()
const whenDialogOpen = async () => {
    const url = `produtos/${props.idClienteAddress}`
    const responseOrder = await axios.get(url)
    const { data: orderData } = responseOrder

    console.log(orderData)

    const responseDistributor = orderData[1];
    const responseAddress = orderData[2];
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
        products: orderData[0],
        distributor,
        address,
        distributorExpedient: orderData[6],
        distributorTaxes: orderData[4],
    }
}

const handleDialogOpen = () => {
    props.open && whenDialogOpen()
    return props.open
}

const handleRealizarPedido = (payload) => {
    var url = "pedidos";
    const response = axios.post(url, payload)
    console.log(payload)
    renderToast(response)
}

</script>

<template>
    <Dialog v-bind="forwarded" :open="handleDialogOpen()" @update:open="(op) => toggleDialog()">
        <DialogContent class="sm:max-w-3xl">
            <DataTableProducts :create-order-data="createOrderData" @callback:payload-pedido="handleRealizarPedido" />
        </DialogContent>
    </Dialog>
</template>
