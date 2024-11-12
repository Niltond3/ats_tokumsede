<script setup>
import { defineComponent, h, markRaw, onMounted, ref } from 'vue'
import axios from 'axios'
import {
    DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger, DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger, DropdownMenuPortal
} from '@/components/ui/dropdown-menu'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { MoreVertical } from 'lucide-vue-next'
import DialogConfirmAction from '../../DialogConfirmAction.vue'
import DialogEditClient from './DialogEditClient.vue'

const props = defineProps({
    payloadData: { type: null, required: true },
    dataTable: { type: null, required: true },
})

const { rowData } = props.payloadData
const { id: idCliente } = rowData

function copy() {
    navigator.clipboard.writeText(props.payloadData)
}

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})

const renderToast = (promise, status) => {
    toast.promise(promise, {
        loading: 'Aguarde...',
        success: (data) => {
            console.log(data)
            props.dataTable.ajax.reload()
            return markRaw(CustomDiv('sucesso', `O cliente ${idCliente} foi ${status} com sucesso!`));
        },
        error: (data) => {
            console.log(data)
            return markRaw(CustomDiv('Error', data.response.data));
        }
    });
}

const handleStatusClientChange = ({ id, status }) => {
    var url = `clientes/${idCliente}`
    const response = axios.put(url, { status: id })
    renderToast(response, status)
}

const handleUpdateDataTable = () => props.dataTable.ajax.reload()

</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="transition-colors text-cyan-700 p-0">
                <span class="sr-only">Abrir Menú</span>
                <MoreVertical class="w-6 h-6" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="border border-slate-200">
            <DropdownMenuLabel>Ações</DropdownMenuLabel>
            <DropdownMenuItem class="gap-2 text-muted-foreground cursor-pointer"
                @click="() => props.dataTable.ajax.reload()">
                <i class="ri-eye-fill text-info"></i>
                Visualizar Cliente
            </DropdownMenuItem>
            <DialogEditClient :client-details="rowData" @update:data-table="handleUpdateDataTable" />
            <DropdownMenuSeparator />
            <DropdownMenuSub>
                <DropdownMenuSubTrigger>
                    <span>Outros</span>
                </DropdownMenuSubTrigger>
                <DropdownMenuPortal>
                    <DropdownMenuSubContent>
                        <DialogConfirmAction
                            @on:confirm="() => handleStatusClientChange({ id: 2, status: 'inativado' })"
                            dialog-description="Deseja realmente inativar o cliente?" dialog-title="Inativar Cliente"
                            trigger-icon="ri-pause-circle-fill" trigger-label="Inativar Cliente" variant="warning" />
                        <DialogConfirmAction @on:confirm="() => handleStatusClientChange({ id: 3, status: 'excluido' })"
                            dialog-description="Deseja realmente Excluir o cliente?" dialog-title="Excluir Cliente"
                            trigger-icon="ri-delete-bin-6-fill" trigger-label="Excluir Cliente" variant="danger" />
                    </DropdownMenuSubContent>
                </DropdownMenuPortal>
            </DropdownMenuSub>

        </DropdownMenuContent>
    </DropdownMenu>
</template>
