<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from "radix-vue";
import {
    Dialog,
    DialogContent,
} from '@/components/ui/dialog'
import { DataTableProducts } from '../../DataTableProducts'
import { utf8Decode } from '@/util';
import renderToast from '@/components/renderPromiseToast';
import { Skeleton } from '@/components/ui/skeleton'

const props = defineProps({
    open: { type: Boolean, required: false },
    toggleDialog: { type: Function, required: false },
    idClienteAddress: { type: String, required: false },
    clientName: { type: String, required: false },
    setTab: { type: Function, required: true },
});

const isLoading = ref(true); // Estado de carregamento

const createOrderData = ref()

const updateTable = ref(false)

const emits = defineEmits(["update:modelValue", "update:dataTable"]);

const forwarded = useForwardPropsEmits(props, emits);


const whenDialogOpen = () => {
    const url = `produtos/${props.idClienteAddress}`
    const promise = axios.get(url)

    renderToast(promise, 'carregando produtos', 'Produtos carregados', (responseOrder) => {
        const { data: orderData } = responseOrder
        const responseDistributor = orderData[1];
        const responseAddress = orderData[2];
        const address = {
            ...responseAddress,
            "logradouro": utf8Decode(responseAddress.logradouro || ''),
            "bairro": utf8Decode(responseAddress.bairro || ''),
            "complemento": utf8Decode(responseAddress.complemento || ''),
            "cidade": utf8Decode(responseAddress.cidade || ''),
            "referencia": utf8Decode(responseAddress.referencia || ''),
            "apelido": utf8Decode(responseAddress.apelido || ''),
            "observacao": utf8Decode(responseAddress.observacao || ''),
        }

        const distributor = {
            ...responseDistributor,
            nome: utf8Decode(responseDistributor.nome),
        }
        const products = orderData[0]
        createOrderData.value = {
            clientName: props.clientName,
            products,
            distributor,
            address,
            distributorExpedient: orderData[6],
            distributorTaxes: orderData[4],
        }
        isLoading.value = false
    })
}

const handleDialogOpen = () => {
    props.open && whenDialogOpen()
    return props.open
}

const handleRealizarPedido = (payload) => {
    var url = "pedidos";
    const promise = axios.post(url, payload)
    renderToast(promise, 'realizando pedido', 'Pedido realizado com sucesso!', () => {
        props.toggleDialog()
        props.setTab('pedidos')
    })
}

const handleSpecialOfferCreated = (isCreated) => updateTable.value = isCreated

const handleToggleDialog = () => {
    if (updateTable.value) emits('update:dataTable', true)
    props.toggleDialog()
}

</script>

<template>
    <Dialog v-bind="forwarded" :open="handleDialogOpen()" @update:open="handleToggleDialog">
        <DialogContent class="sm:max-w-3xl">
            <div v-if="isLoading">
                <div class="border rounded-md border-gray-200 relative">
                    <div class="flex flex-col gap-1">
                        <Skeleton class="h-12 w-full rounded-md" />
                        <Skeleton class="h-[235px] w-full" />
                    </div>
                    <div class="flex flex-col gap-1 p-2">
                        <Separator />
                        <div class="flex items-center h-11 justify-around ">
                            <div class="flex gap-8">
                                <span class="text-sm font-medium relative text-info">
                                    <span
                                        class="absolute -top-7 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">
                                        Produtos
                                    </span>
                                    <Skeleton class="w-14 h-5" />
                                </span>
                                <span class="text-sm font-medium relative text-info">
                                    <span
                                        class="absolute -top-7 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">
                                        Entrega
                                    </span>
                                    <Skeleton class="w-14 h-5" />
                                </span>
                                <span class="text-sm font-medium relative text-info">
                                    <span
                                        class="absolute -top-7 text-gray-500 text-xs -translate-x-1/2 left-1/2 bg-white p-1">
                                        Total
                                    </span>
                                    <Skeleton class="w-14 h-5" />
                                </span>
                            </div>
                        </div>
                        <Separator label="Detalhes" class="z-100" />
                        <div class="flex flex-wrap gap-2 p-2 sm:h-14 justify-center">
                            <Skeleton class="w-1/4 h-10" />
                            <Separator orientation="vertical" class="" />
                            <Skeleton class="w-1/4 h-10" />
                            <Separator orientation="vertical" class="hidden sm:block" />
                            <Skeleton class="w-1/4 h-10" />
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <DataTableProducts :create-order-data="createOrderData" @callback:payload-pedido="handleRealizarPedido"
                    @update:special-offer-created="handleSpecialOfferCreated" />
            </div>
        </DialogContent>
    </Dialog>
</template>
