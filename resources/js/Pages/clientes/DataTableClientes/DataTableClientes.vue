<script setup>
import { ref, onMounted, defineComponent, h, markRaw, defineProps, onBeforeMount } from 'vue';
import axios from 'axios'
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-autofill-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-dt';
import 'datatables.net-scroller-dt';
import 'datatables.net-searchbuilder-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import 'datatables.net-staterestore-dt';
import DialogCreateOrder from './components/DialogCreateOrder.vue';
import { utf8Decode } from '../../../util';
import { dialogState } from '../useToggleDialog';
import languagePtBR from './dataTablePtBR.mjs';
import rowChildtable from './rowChildTable';
import { formatOrder } from '../utils';

import DialogShowOrder from './components/DialogShowOrder.vue';
import DropdownActionClient from './components/DropdownActionClient.vue';
import DialogRegisterClient from './components/DialogRegisterClient.vue';
import DialogRegisterAddress from './components/DialogRegisterAddress.vue';

DataTable.use(DataTablesCore);

const props = defineProps({
    setTab: { type: Function, required: true },
})

const [isOpen, toggleDialog] = dialogState();
const [openShowOrderDialog, toggleShowOrderDialog] = dialogState();
const [openRegisterAddress, toggleRegisterAddress] = dialogState();

const idClienteAddress = ref('')
const idClient = ref('')
const idOrder = ref('')
let dt;
const table = ref();


onMounted(() => {
    dt = table.value.dt;

    const format = (d) => {
        // `d` is the original data object for the row
        return (rowChildtable(d));
    }

    const dragScrollList = (elementId) => {
        console.log(`Drag scroll Element: ${elementId}`);
        const ele = document.getElementById(elementId);
        ele.style.cursor = 'grab';

        let pos = { top: 0, left: 0, x: 0, y: 0 };

        const mouseDownHandler = function (e) {
            ele.style.cursor = 'grabbing';
            ele.style.userSelect = 'none';

            pos = {
                left: ele.scrollLeft,
                top: ele.scrollTop,
                // Get the current mouse position
                x: e.clientX,
                y: e.clientY,
            };

            document.addEventListener('mousemove', mouseMoveHandler);
            document.addEventListener('mouseup', mouseUpHandler);
        };

        const mouseMoveHandler = function (e) {
            // How far the mouse has been moved
            const dx = e.clientX - pos.x;
            const dy = e.clientY - pos.y;

            // Scroll the element
            ele.scrollTop = pos.top - dy;
            ele.scrollLeft = pos.left - dx;
        };

        const mouseUpHandler = function () {
            ele.style.cursor = 'grab';
            ele.style.removeProperty('user-select');

            document.removeEventListener('mousemove', mouseMoveHandler);
            document.removeEventListener('mouseup', mouseUpHandler);
        };

        // Attach the handler
        ele.addEventListener('mousedown', mouseDownHandler);
    }
    $('#datatable-clientes tbody').on('click', 'td.dt-control', async function () {
        var tr = $(this).closest('tr');
        var row = dt.row(tr);
        const client = row.data()

        var url = "clientes/" + client.id;

        const response = await axios.get(url)

        const pedidos = response.data.pedidos.map((pedido) => formatOrder(pedido))


        const childData = {
            ...client,
            pedidos
        }

        if (row.child.isShown()) {
            return row.child.hide(); // This row is already open - close it
        }

        row.child(format(childData)).show();// Open this row

        dragScrollList('enderecos')
        dragScrollList('pedidos')
    });
    $('#datatable-clientes').on("click", '.iniciarPedido', function () {
        idClienteAddress.value = this.id
        toggleDialog()
    })
    $("#datatable-clientes").on("click", ".novoEndereco", function () {
        idClient.value = this.id
        toggleRegisterAddress()
    })
    $('#datatable-clientes').on("click", '.visualizarPedido', function () {
        idOrder.value = parseInt(this.id)
        toggleShowOrderDialog()
    })
});

const columns = [
    {
        className: 'dt-control',
        orderable: false,
        data: null,
        defaultContent: '',
        render: '#dt-control'
    },
    { data: 'nome', title: 'Nome' },
    { data: 'tipoPessoa', title: 'CPF/CNPJ', searchable: false },
    { data: 'telefone', title: 'Telefone' },
    { data: 'outrosContatos', title: 'Outros Contatos' },
    { data: 'rating', title: 'Rating', searchable: false },
    { data: 'opcoes', title: 'Opções', render: '#actions', searchable: false },
    { data: 'enderecos[].logradouro', name: 'enderecos.logradouro', visible: false },
    { data: 'enderecos[].bairro', name: 'enderecos.bairro', visible: false },
    { data: 'enderecos[].numero', name: 'enderecos.numero', visible: false },
    { data: 'enderecos[].cidade', name: 'enderecos.cidade', visible: false },
    { data: 'enderecos[].estado', name: 'enderecos.estado', visible: false },
    { data: 'dddTelefone', visible: false }
]

const options = {
    language: languagePtBR,
    serverSide: true,
    processing: true,
}

const ajax = {
    url: 'clientes', dataFilter: function (data) {
        const obj = JSON.parse(data)
        const newData = obj.data.map((client) => {


            const nome = utf8Decode(client.nome)
            const enderecos = client.enderecos.map((address) => {
                return {
                    ...address,
                    logradouro: utf8Decode(address.logradouro),
                    bairro: utf8Decode(address.bairro),
                    cidade: utf8Decode(address.cidade),
                    observacao: utf8Decode(address.observacao || ''),
                    referencia: utf8Decode(address.referencia || ''),
                    apelido: utf8Decode(address.apelido || ''),
                }
            })

            const newClient = { ...client, nome, enderecos }

            return newClient
        })
        const newObj = { ...obj, data: newData };

        return JSON.stringify(newObj);
    },
    error: function (err) {
        console.log(err)
    }
}

const handleUpdateDataTable = () => dt.ajax.reload();

</script>

<template>
    <div>
        <DialogCreateOrder :open="isOpen" :toggleDialog="toggleDialog" :id-cliente-address="idClienteAddress"
            :set-tab="props.setTab" />

        <DialogShowOrder :open="openShowOrderDialog" :toggleDialog="toggleShowOrderDialog" :order-id="idOrder" />

        <DialogRegisterClient @update:data-table="handleUpdateDataTable" />
        <DialogRegisterAddress :open="openRegisterAddress" :toggleDialog="toggleRegisterAddress" :id-client="idClient"
            @update:data-table="handleUpdateDataTable" />
        <DataTable id="datatable-clientes"
            class="display [&_thead]:bg-info [&_thead]:text-[#F3F9FD] [&_tbody>tr>td.dt-control]:before:hidden [&_tbody>tr.dt-hasChild_span.transition-all]:rotate-90 [&_tbody>tr.dt-hasChild_div.relative]:bg-danger"
            :columns="columns" :ajax="ajax" :options="options" ref="table" language="language">
            <template #actions="data">
                <DropdownActionClient :payloadData="data" :data-table="dt" />
            </template>
            <template #dt-control>
                <div class="relative group m-auto w-[14px] h-[14px] bg-info rounded-full p-2">
                    <span
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 block w-[2px] h-[10px] bg-white group-hover:rotate-90 transition-all"></span>
                    <span
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 block h-px w-[10px] bg-white"></span>
                </div>
            </template>
        </DataTable>
    </div>
</template>

<style>
@import 'datatables.net-dt';
</style>
