<script setup>
import { defineComponent, h, markRaw, ref } from 'vue'
import axios from 'axios'
import {
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger, DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger, DropdownMenuPortal
} from '@/components/ui/dropdown-menu'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { MoreVertical } from 'lucide-vue-next'
import DialogSelectDeliveryMan from './DialogSelectDeliveryMan.vue'
import DialogEditOrder from './DialogEditOrder.vue'
import DialogShowOrder from './DialogShowOrder.vue'
import DialogConfirmAction from './DialogConfirmAction.vue'
import { Link, usePage, } from '@inertiajs/vue3';

const page = usePage()

const props = defineProps({
    payloadData: { type: null, required: true },
    entregadores: { type: null, required: true },
    loadTable: { type: Function, required: true },
})

const orderStatus = ref(props.payloadData.status.label)

const { tipoAdministrador } = page.props.auth.user

const emit = defineEmits(['callback:edited-order'])

const { id: idPedido, } = props.payloadData

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})

const renderToast = (promise, status) => {
    toast.promise(promise, {
        loading: 'Aguarde...',

        success: (data) => {
            props.loadTable()
            return markRaw(CustomDiv('sucesso', `O pedido ${idPedido} foi ${status} com sucesso!`));
        },
        error: (data) => markRaw(CustomDiv('Error', data.response.data)),
    });
}

const handleAceitar = () => {
    var url = `pedidos/aceitar/${idPedido}`
    const promise = axios.put(url)
    renderToast(promise, 'aceito')
}

const handleDespachar = (deliveryMan) => {
    var url = `pedidos/despachar/${idPedido}`
    const promise = axios.put(url, { entregador: deliveryMan })
    renderToast(promise, 'despachado')
}

const handleEntregar = (id) => {
    var url = `pedidos/entregar/${idPedido}`
    const promise = axios.put(url)
    renderToast(promise, 'entregue')
}

const handleCancelar = (reson) => {
    var url = `pedidos/recusar/${idPedido}`
    const promise = axios.put(url, { retorno: reson })
    renderToast(promise, 'Cancelado')
}

const handleEditOrder = () => emit('callback:edited-order')


</script>

<template>
    <div class="min-[426px]:hidden flex gap-3">
        <DialogShowOrder :dropdown="false" :order-id="idPedido" />
        <DialogEditOrder v-if="tipoAdministrador === 'Administrador'" :dropdown="false" :order-id="idPedido"
            @callback:edit-order="handleEditOrder" />
        <button v-if="orderStatus == 'Pendente'"
            class="h-8 w-8 rounded-full bg-success/80 focus:bg-success text-white shadow-sm hover:shadow-md transition-all"
            @click="handleAceitar()">
            <i class="ri-check-fill"></i>
        </button>
        <DialogSelectDeliveryMan v-if="orderStatus == 'Aceito'" :dropdown="false"
            @on:delivery-man-selected="handleDespachar" :entregadores="entregadores" />
        <button v-if="orderStatus == 'Despachado'"
            class="h-8 w-8 rounded-full bg-accepted/80 focus:bg-accepted text-white shadow-sm hover:shadow-md transition-all"
            @click="handleEntregar()">
            <i class="ri-check-double-fill"></i>
        </button>
        <DialogConfirmAction :dropdown="false" @on:confirm="handleCancelar" dialog-title="Cancelar Pedido"
            trigger-icon="ri-close-circle-fill" trigger-label="Cancelar" variant="danger" :text-reson="true" />
    </div>
</template>
