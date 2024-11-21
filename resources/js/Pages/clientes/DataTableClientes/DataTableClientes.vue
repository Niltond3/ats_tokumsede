<script setup>
import { ref, onMounted, defineComponent, h, markRaw, defineProps, onBeforeMount, getCurrentInstance } from 'vue';
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
import { utf8Decode } from '../../../util';
import { dialogState } from '../../../hooks/useToggleDialog';
import languagePtBR from './dataTablePtBR.mjs';
import { formatOrder } from '../utils';
import { toast } from 'vue-sonner';

import rowChildtable from './components/rowChildTable/index';
import DialogShowOrder from './components/DialogShowOrder.vue';
import DialogCreateOrder from './components/DialogCreateOrder.vue';
import DropdownActionClient from './components/DropdownActionClient.vue';
import DialogRegisterClient from './components/DialogRegisterClient.vue';
import DialogRegisterAddress from './components/DialogRegisterAddress.vue';
import DialogConfirmAddressDelete from './components/DialogConfirmAddressDelete.vue';

DataTable.use(DataTablesCore);

const props = defineProps({
    setTab: { type: Function, required: true },
})

const { isOpen, toggleDialog } = dialogState();
const { isOpen: openShowOrderDialog, toggleDialog: toggleShowOrderDialog } = dialogState();
const { isOpen: openRegisterAddress, toggleDialog: toggleRegisterAddress } = dialogState();
const { isOpen: openEditAddress, toggleDialog: toggleEditAddress } = dialogState();
const { isOpen: openConfirmDialog, toggleDialog: toggleConfirmDialog } = dialogState();

const idClienteAddress = ref('')
const idClient = ref('')
const idAddress = ref('')
const idOrder = ref('')
const address = ref({})
const addressTarget = ref({})

let dt;
const table = ref();

onMounted(() => {
    dt = table.value.dt;
    // `d` is the original data object for the row


    const format = (d) => rowChildtable(d)
    //

    const dragScrollList = (elementId) => {
        const ele = document.getElementById(elementId);
        ele.style.cursor = 'pointer';

        let pos = { top: 0, left: 0, x: 0, y: 0 };

        const mouseDownHandler = function (e) {
            e.preventDefault();
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
            e.preventDefault();
            // How far the mouse has been moved
            const dx = e.clientX - pos.x;
            const dy = e.clientY - pos.y;

            // Scroll the element
            ele.scrollTop = pos.top - dy;
            ele.scrollLeft = pos.left - dx;
        };

        const mouseUpHandler = function () {
            ele.style.cursor = 'pointer';
            ele.style.removeProperty('user-select');

            document.removeEventListener('mousemove', mouseMoveHandler);
            document.removeEventListener('mouseup', mouseUpHandler);
        };

        // Attach the handler
        ele.addEventListener('mousedown', mouseDownHandler);
        console.log(`Drag scroll Element: ${elementId}`);
    }

    $('.dt-search').addClass('flex items-center py-2 px-4 gap-2 !text-info/80')

    $('.dt-search > label').html(/*html*/`
    <span class="hidden">pesquisar</span>
    <i class="ri-search-2-fill"></i>
    `)

    const inputClasses = 'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary !border-input placeholder:text-info/50 !text-info/80'

    $('.dt-search > input').addClass(inputClasses)

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
            addressTarget.value == null && dt.ajax.reload()
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
    $('#datatable-clientes').on("click", '.editarEndereco', function () {

        const idAddress = this.getAttribute('addr_id')
        const idClient = this.getAttribute('cli_id')

        const data = dt.data()

        address.value = Object.values(data).filter(cli => cli.id == idClient)[0].enderecos.filter(addr => addr.id == idAddress)[0]

        toggleEditAddress()
    })
    $("#datatable-clientes").on("click", ".novoEndereco", function () {
        idClient.value = this.id
        toggleRegisterAddress()
    })
    $('#datatable-clientes').on("click", '.visualizarPedido', function () {
        idOrder.value = parseInt(this.id)
        toggleShowOrderDialog()
    })
    $('#datatable-clientes').on("click", '.copiarEndereco', function () {

        const data = dt.data()

        const payload = Object.values(data).filter(cli => cli.id == idClient.value)[0].enderecos.filter(addr => addr.id == idAddress.value)[0]
        const clipboard = `
------------- endereço -------------
${payload.cep ? 'CEP: ' + payload.cep : ''}
Cidade: ${payload.cidade}
${payload.estado !== null ? 'Estado: ' + payload.estado : ''}
${payload.apelido ? 'Apelido: ' + payload.apelido : ''}
Logradouro: ${payload.logradouro}
Número: ${payload.numero}
Bairro: ${payload.bairro}
${payload.complemento ? 'Complemento: ' + payload.complemento : ''}
${payload.referencia ? 'Referência: ' + payload.referencia : ''}
${payload.observacao ? 'Observação: ' + payload.observacao : ''}
`.replace(/(^[ \t]*\n)/gm, "")

        navigator.clipboard.writeText(clipboard)
        toast.info('Copiado para a área de transferência')
    })
    $('#datatable-clientes').on("click", '.excluirEndereco', function () {
        toggleConfirmDialog()
    })
    $('#datatable-clientes').on("long-press", '.deleteEndereco', function (e) {

        idClient.value = e.target.id
        idAddress.value = e.target.getAttribute('addr_id')

        e.target.setAttribute('aria-selected', e.target.getAttribute('aria-selected') === 'true' ? 'false' : 'true');

        addressTarget.value = e.target

        e.target.parentNode.classList.toggle("[&>li[aria-selected='false']]:pointer-events-none");

    })
    $('#datatable-clientes').on("click", '.accordionController', function (e) {
        e.target.classList.toggle('after:!content-["-"]')
        const panel = e.target.parentNode.nextElementSibling
        panel.firstChild.nextElementSibling.classList.toggle('!max-h-[11rem]')
    })
    // accordionController
    // var acc = document.getElementsByClassName("accordionController");
    // var i;

    // for (i = 0; i < acc.length; i++) {
    //     acc[i].addEventListener("click", function () {
    //         this.classList.toggle("active");
    //         var panel = this.nextElementSibling;
    //         if (panel.style.maxHeight) {
    //             panel.style.maxHeight = null;
    //         } else {
    //             panel.style.maxHeight = panel.scrollHeight + "px";
    //         }

});

const columns = [
    {
        className: 'dt-control',
        orderable: false,
        data: null,
        defaultContent: '',
        render: '#dt-control',
        responsivePriority: 1
    },
    { data: 'nome', title: 'Nome', responsivePriority: 2 },
    { data: 'tipoPessoa', title: 'CPF/CNPJ', searchable: false, responsivePriority: 6 },
    { data: 'telefone', title: 'Telefone', responsivePriority: 3 },
    { data: 'outrosContatos', title: 'Outros Contatos', responsivePriority: 4 },
    { data: 'rating', title: 'Rating', searchable: false, responsivePriority: 5 },
    { data: 'opcoes', title: 'Opções', render: '#actions', searchable: false, responsivePriority: 1 },
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
    responsive: {
        details: false,
        breakpoints: [
            { name: 'bigdesktop', width: Infinity },
            { name: 'meddesktop', width: 1480 },
            { name: 'smalldesktop', width: 1280 },
            { name: 'medium', width: 1188 },
            { name: 'tabletl', width: 1024 },
            { name: 'btwtabllandp', width: 848 },
            { name: 'tabletp', width: 768 },
            { name: 'mobilel', width: 480 },
            { name: 'mobilep', width: 320 }
        ]
    },
    layout: {
        top: 'search',
        topStart: null,
        topEnd: null,
        bottomStart: 'info',
        bottomEnd: 'paging'
    }
}

const ajax = {
    url: 'clientes', dataFilter: function (data) {
        const obj = JSON.parse(data)
        const newData = obj.data.map((client) => {

            const nome = utf8Decode(client.nome)
            const enderecos = client.enderecos.map((address) => {
                console.log(address.cidade)
                return {
                    ...address,
                    logradouro: utf8Decode(address.logradouro || ''),
                    bairro: utf8Decode(address.bairro || ''),
                    cidade: utf8Decode(address.cidade || ''),
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

const handleDeleteAddress = (confirm) => {
    console.log('handleDeleteAddress' + confirm)

    if (confirm === false) return toggleConfirmDialog()

    let container = addressTarget.value;

    container.classList.add('w-0');
    container.classList.add('opacity-0');
    container.classList.add('hidden');

    container.parentNode.classList.toggle("[&>li[aria-selected='false']]:pointer-events-none");
    addressTarget.value = null
    toggleConfirmDialog()
    container.ontransitionend = function () {
    }
}

</script>

<template>
    <div class="[&_.dt-search]:relative [&_.dt-search>label]:ri-search-2-fill">

        <DialogCreateOrder :open="isOpen" :toggleDialog="toggleDialog" :id-cliente-address="idClienteAddress"
            :set-tab="props.setTab" />
        <DialogShowOrder :open="openShowOrderDialog" :toggleDialog="toggleShowOrderDialog" :order-id="idOrder" />
        <DialogRegisterClient @update:data-table="handleUpdateDataTable" />
        <DialogRegisterAddress :open="openRegisterAddress" :toggleDialog="toggleRegisterAddress" :id-client="idClient"
            @update:data-table="handleUpdateDataTable" />
        <DialogRegisterAddress :open="openEditAddress" :toggleDialog="toggleEditAddress"
            @update:data-table="handleUpdateDataTable" :address-details="address" />
        <DialogConfirmAddressDelete dialog-title="Excluir Endereço" variant="danger" :id-address="idAddress"
            @delete:confirm="handleDeleteAddress" dialog-description="Tem certeza que deseja excluir o endereço?"
            :open="openConfirmDialog" />
        <DataTable id="datatable-clientes"
            class="display [&_thead]:bg-info [&_thead]:text-[#F3F9FD] [&_tbody>tr>td.dt-control]:before:hidden [&_tbody>tr.dt-hasChild_span.transition-all]:rotate-90 [&_tbody>tr.dt-hasChild_div.relative]:bg-danger"
            :columns="columns" :ajax="ajax" :options="options" ref="table" language="language">
            <template #actions="data">
                <DropdownActionClient :payloadData="data" :data-table="dt" />
            </template>
            <template #dt-control>
                <div class="relative group m-auto w-[14px] h-[14px] bg-info rounded-full p-2">
                    <span
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 block w-[2px] h-[10px] bg-white transition-all"></span>
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
