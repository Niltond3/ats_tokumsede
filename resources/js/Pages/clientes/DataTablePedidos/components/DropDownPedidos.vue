<script setup>
import { defineComponent, h, markRaw } from 'vue'
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

const props = defineProps({
    payloadData: { type: null, required: true },
    entregadores: { type: null, required: true },
    loadTable: { type: Function, required: true },
})

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
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="transition-colors text-cyan-700 p-0">
                <span class="sr-only">Abrir Menú</span>
                <MoreVertical class="w-6 h-6" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Ações</DropdownMenuLabel>
            <DialogShowOrder :order-id="idPedido" />
            <DialogEditOrder :order-id="idPedido" @callback:edit-order="handleEditOrder" />
            <DropdownMenuSeparator />
            <DropdownMenuSub>
                <DropdownMenuSubTrigger>
                    <span>Status</span>
                </DropdownMenuSubTrigger>
                <DropdownMenuPortal>
                    <DropdownMenuSubContent>
                        <DropdownMenuItem class="cursor-pointer" @click="handleAceitar()">Aceitar</DropdownMenuItem>
                        <DialogSelectDeliveryMan @on:delivery-man-selected="handleDespachar"
                            :entregadores="entregadores" />
                        <DropdownMenuItem class="cursor-pointer" @click="handleEntregar()">Entregar</DropdownMenuItem>
                        <DialogConfirmAction @on:confirm="handleCancelar" dialog-title="Cancelar Pedido"
                            trigger-icon="ri-close-circle-fill" trigger-label="Cancelar" variant="danger"
                            :text-reson="true" />
                    </DropdownMenuSubContent>
                </DropdownMenuPortal>
            </DropdownMenuSub>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
