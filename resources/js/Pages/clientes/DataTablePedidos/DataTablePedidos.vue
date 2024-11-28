<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import languagePtBR from './dataTablePtBR.mjs';
import 'datatables.net-buttons-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import { utf8Decode, dateToISOFormat } from '@/util';
import { getStatusString } from '../utils';
import DropDownPedidos from './components/DropDownPedidos.vue';
import ActionOrders from './components/ActionOrders.vue';
import { twMerge } from 'tailwind-merge';
import observeNewOrders from './components/observeNewOrders';
import renderToast from '@/components/renderPromiseToast'

DataTable.use(DataTablesLib);

const props = defineProps({
    setTab: { type: Function, required: false },
    ajustClass: { type: String, required: false },
})

let dt;
const table = ref();
const data = ref([]);
const entregadores = ref([]);



const loadTableData = () => {
    var urlPedidos = 'pedidos';

    const pedidos = axios.get(urlPedidos);

    renderToast(pedidos, 'Atualizando Tabela, aguarde...', 'tabela de pedidos atualizada', (response) => {
        entregadores.value = response.data[7];

        const concatArray = [...response.data[0], ...response.data[1], ...response.data[2], ...response.data[3], ...response.data[4]]

        const scheduledOrders = response.data[5].filter((pedido, index, self) => {
            const fid = concatArray.find(p => {
                const dateIso = dateToISOFormat(`${p.dataAgendada} ${p.horaInicio}`)
                const currentDate = new Date();
                const scheduleDate = new Date(dateIso);
                return p.id === pedido.id && currentDate >= scheduleDate
            })
            if (fid === undefined) return pedido
        })

        const orders = [...concatArray, ...scheduledOrders]

        const newData = orders.map(pedido => {
            const status = getStatusString(pedido.agendado, pedido.dataAgendada, pedido.horaInicio, pedido.status)

            const distribuidorNome = utf8Decode(pedido.distribuidor.nome)
            const clienteNome = utf8Decode(pedido.cliente.nome)

            return {
                ...pedido, status,
                distribuidor: {
                    ...pedido.distribuidor,
                    nome: distribuidorNome.substr(distribuidorNome.indexOf(" ") + 1)
                },
                cliente: {
                    ...pedido.cliente,
                    nome: clienteNome
                }
            }
        })
        
        data.value = newData
    })
}

const handleLoadTableData = () => {
    console.log('handleLoadTableData')
    loadTableData()
}

onMounted(() => {
    dt = table.value.dt;
    $('.dt-search').addClass('flex items-center py-2 px-1 gap-2 !text-info/80 !mb-[30px] min-[768px]:!mb-[10px]')

    $('.dt-search > label').html(/*html*/`
    <span class="hidden">pesquisar</span>
    <i class="ri-search-2-fill"></i>
    `)

    const inputClasses = 'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary !border-input placeholder:text-info/50 !text-info/80'

    $('.dt-search > input').addClass(inputClasses)

    loadTableData();
    window.setInterval(observeNewOrders(loadTableData), 10000);
})

const columns = [
    { data: 'id', title: '#', responsivePriority: 7 },
    { data: 'distribuidor.nome', title: 'Distribuidor', responsivePriority: 3 },
    { data: 'cliente.nome', title: 'Cliente', responsivePriority: 2 },
    { data: 'cliente.rating', render: '#rating', title: 'Rating', responsivePriority: 6 },
    { data: 'horarioPedido', title: 'Data do Pedido' },
    { data: 'dataAgendada', title: 'Agendamento', responsivePriority: 4 },
    { data: 'status.label', title: 'status', responsivePriority: 5 },
    {
        data: 'cliente.nome',
        render: '#action',
        title: 'ações',
        responsivePriority: 1
    },
    { data: 'cliente.dddTelefone', title: 'cliente.dddTelefone', visible: false },
    { data: 'cliente.telefone', title: 'cliente.telefone', visible: false },
    { data: 'endereco.logradouro', title: 'endereco.logradouro', visible: false },
    { data: 'endereco.bairro', title: 'endereco.bairro', visible: false },
    { data: 'endereco.numero', title: 'endereco.numero', visible: false },
    { data: 'endereco.estado', title: 'endereco.estado', visible: false },
    { data: 'endereco.cidade', title: 'endereco.cidade', visible: false },
];

const options = {
    language: languagePtBR,
    processing: true,
    deferRender: true,
    orderClasses: false,
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
    columnDefs: [
        {
            orderable: false,
            render: (data, type, row) => {
                const state = (icon, peso) =>
                    `
                    <div class="flex items-center gap-1">
                        <i class="${icon} text-lg"></i>
                        <span class="w-0 opacity-0 pointer-events-none">${peso}</span>
                         <span class="hidden min-[768px]:block"> ${data} </span>
                    </div >
                 `
                const getType = {
                    Pendente: state('ri-error-warning-line', 1),
                    Aceito: state('ri-check-line', 2),
                    Despachado: state('ri-e-bike-2-line', 3),
                    Entregue: state('ri-check-double-line', 4),
                    Cancelado: state('ri-close-circle-line', 5),
                    Agendado: state('ri-calendar-schedule-line', 6),
                }

                const statusKey = (data == 'Cancelado pelo Usuário' || data == 'Não Localizado' || data == 'Trote' || data == 'Recusado') ? 'Cancelado' : data

                return getType[statusKey]
            },
            searchPanes: {
                show: true,
                controls: false,
                className: twMerge(
                    props.ajustClass,
                    '[&>div.dtsp-topRow]:hidden',
                    '[&_.dt-layout-row]:!m-0',
                    '[&_.dt-scroll]:!m-0',
                    '[&_.dts_label]:hidden',
                    '[&_tr]:cursor-pointer',
                    '[&_td]:!pb-[14px]  [&_td]:rounded-t-md',
                    '[&_tbody]:flex [&_tbody]:justify-center',
                    '[&_input]:opacity-0 [&_input]:pointer-events-none',
                    '[&_tr_div]:flex [&_tr_div]:gap-2 [&_tr_div]:transition-transform [&_tr_div]:text-[#1e88e5]/80',
                    '[&_tr.selected>td]:!shadow-[inset_0_0_0_9999px_rgba(30,136,229,1)] [&_tr.selected>td]:font-bold',
                    '[&_.dt-scroll-body]:!overflow-hidden [&_.dt-scroll-body]:!border-none',
                    '[&_tr>.dtsp-nameCont>span>div]:bg-success',
                    '[&_tr.selected_.dtsp-nameCont]:translate-y-[5px]',
                    '[&_.dt-scroll-head]:hidden',
                    '[&_tr.selected_div]:!text-white',
                    '[&_.dt-scroll-body]:!h-[46px]',
                    '[&_table]:flex',
                    '[&_table>colgroup]:hidden',
                    '[&_table>thead]:hidden',
                    'absolute top-[55px] right-0',
                    'min-[768px]:top-[50px]',
                    'w-full',
                    '!overflow-hidden bg-transparent'),
            },
            targets: [6]
        }
    ],
    layout: {
        top: 'search',
        topStart: {
            searchPanes: {
                layout: 'columns-1',
                cascadePanes: true,
            },
        },
        topEnd: null,
    },
}

const badgeClasses = 'font-normal inline-block px-2 text-[75%] text-center whitespace-nowrap align-baseline rounded text-white'

</script>


<template>
    <DataTable class="display [&_thead]:bg-info [&_thead]:text-[#F3F9FD]" :columns="columns" :data="data"
        :options="options" ref="table" language="language">
        <template #action="data">
            <div>
                <DropDownPedidos :payloadData="data.rowData" :entregadores="entregadores"
                    @callback:edited-order="handleLoadTableData" />
                <ActionOrders :payloadData="data.rowData" :entregadores="entregadores" :loadTable="loadTableData" />
            </div>
        </template>
        <template #rating="data">
            <div>
                <span v-if="data.rowData.cliente.rating > 0" :class="badgeClasses" class="bg-success">
                    {{ data.rowData.cliente.rating }}
                </span>
                <span v-else-if="data.rowData.cliente.rating == 0" :class="badgeClasses" class="bg-inverse">
                    {{ data.rowData.cliente.rating }}
                </span>
                <span v-else-if="data.rowData.cliente.rating < -2" :class="badgeClasses" class="bg-danger">
                    {{ data.rowData.cliente.rating }}
                </span>
                <span v-else :class="badgeClasses" class="bg-warning">
                    {{ data.rowData.cliente.rating }}
                </span>
            </div>
        </template>
    </DataTable>
</template>

<style>
@import 'datatables.net-dt';
</style>
