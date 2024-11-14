<script setup>
import axios from 'axios';
import { ref, onMounted, defineProps } from 'vue';
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

DataTable.use(DataTablesLib);

const props = defineProps({
    setTab: { type: Function, required: true },
})

let dt;
const data = ref([]);
const table = ref();
const entregadores = ref([]);

const columns = [
    { data: 'id', title: '#' },
    { data: 'distribuidor.nome', title: 'Distribuidor' },
    { data: 'cliente.nome', title: 'Cliente' },
    { data: 'cliente.rating', render: '#rating', title: 'Rating' },
    { data: 'horarioPedido', title: 'Data do Pedido' },
    { data: 'dataAgendada', title: 'Agendamento' },
    { data: 'status.label', title: 'status' },
    {
        data: 'cliente.nome',
        render: '#action',
        title: 'ações'
    },
    { data: 'cliente.dddTelefone', title: 'cliente.dddTelefone', visible: false },
    { data: 'cliente.telefone', title: 'cliente.telefone', visible: false },
    { data: 'endereco.logradouro', title: 'endereco.logradouro', visible: false },
    { data: 'endereco.bairro', title: 'endereco.bairro', visible: false },
    { data: 'endereco.numero', title: 'endereco.numero', visible: false },
    { data: 'endereco.estado', title: 'endereco.estado', visible: false },
    { data: 'endereco.cidade', title: 'endereco.cidade', visible: false },
];

const loadTableData = async () => {
    var urlPedidos = 'pedidos';
    const response = await axios.get(urlPedidos);

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
}

onMounted(() => {
    dt = table.value.dt;
    /*
         //STATUS
    const PENDENTE = 1;
    const CANCELADO_USUARIO = 2;
    const CANCELADO_NAO_LOCALIZADO = 3;
    const CANCELADO_TROTE = 4;
    const RECUSADO = 5;
    const ACEITO = 8;
    const DESPACHADO = 6;
    const ENTREGUE = 7;
    //FORMA_PAGAMENTO
    const OUTROS = 0;
    const DINHEIRO = 1;
    const CARTAO = 2;
    const PIX = 3;
    const TRANSFERENCIA = 4;
    const IFOOD = 5;
    //ORIGEM
    const APP_ANDROID = 1;
    const APP_IOS = 2;
    const PLATAFORMA = 3;
    */
    loadTableData();
})

const options = {
    language: languagePtBR,
    processing: true,
    deferRender: true,
    orderClasses: false,
    columnDefs: [
        {
            orderable: false,
            render: (data, type, row) => {
                const state = (icon, peso) =>
                    `
                    <div class="flex items-center gap-1">
                        <i class="${icon} text-lg"></i>
                        <span class="w-0 opacity-0 pointer-events-none">${peso}</span>
                         ${data}
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
                className: '[&_.dts_label]:hidden [&_tbody]:flex [&_tbody]:justify-center [&_tr]:cursor-pointer [&_tr_div]:flex [&_td]:!pb-[14px] [&_tr_div]:gap-2 [&_input]:opacity-0  [&_input]:pointer-events-none w-[72%] right-0 mr-[266px] max-h-[150px] absolute top-[-40px] !overflow-hidden bg-transparent [&_tr.selected>td]:!shadow-[inset_0_0_0_9999px_rgba(30,136,229,1)] [&_td]:rounded-t-md [&_tr.selected>td]:font-bold [&_tr.selected_.dtsp-nameCont]:translate-y-[5px] [&_tr_div]:transition-transform [&_.dt-scroll-body]:!overflow-hidden [&_.dt-scroll-head]:hidden [&_tr_div]:text-[#1e88e5]/80 [&_tr.selected_div]:!text-white [&_.dt-scroll-body]:!border-none [&_.dt-scroll-body]:!h-[46px]',
            },
            targets: [6]
        }
    ],
    layout: {
        top1: {
            searchPanes: {
                layout: 'columns-1',
                cascadePanes: true,
            },
        }
    },
}

const badgeClasses = 'font-normal inline-block px-2 text-[75%] text-center whitespace-nowrap align-baseline rounded text-white'

</script>


<template>
    <DataTable class="display [&_thead]:bg-info [&_thead]:text-[#F3F9FD]" :columns="columns" :data="data"
        :options="options" ref="table" language="language">
        <template #action="data">
            <DropDownPedidos :payloadData="data.rowData" :entregadores="entregadores" :loadTable="loadTableData"
                @callback:edited-order="() => loadTableData()" />
        </template>
        <template #rating="data">
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
        </template>
    </DataTable>
</template>

<style>
@import 'datatables.net-dt';
</style>
