<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import 'datatables.net-buttons-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import { utf8Decode, dateToISOFormat, dateToDayMonthYearFormat, checkDate } from '@/util';
import { getStatusString } from '../utils';
import DropDownPedidos from './components/DropDownPedidos.vue';
import ActionOrders from './components/ActionOrders.vue';
import observeNewOrders from './components/observeNewOrders';
import renderToast from '@/components/renderPromiseToast'
import { tableConfig } from './config/tableConfig'
import { toast } from 'vue-sonner';
import { shallowRef } from 'vue';
import { useMemoize } from '@vueuse/core';

DataTable.use(DataTablesLib);

const props = defineProps({
    setTab: { type: Function, required: false },
    ajustClass: { type: String, required: false },
})

const { columns, options } = tableConfig(props.ajustClass)

let dt;
const table = ref();
const data = shallowRef([]);
const orders = ref([]);
const entregadores = ref([]);

const getFormatedScheduleDate = (pedido) => {
    if (!pedido.dataAgendada) return pedido.dataAgendada
    const rawScheduleDate = `${pedido.dataAgendada} ${pedido.horaInicio}`
    return `${checkDate(rawScheduleDate)} às ${pedido.horaInicio}`
}

const memoizedGetStatus = useMemoize((agendado, dataAgendada, horaInicio, status) => {
    return getStatusString(agendado, dataAgendada, horaInicio, status)
})

const transformOrder = (pedido) => {
    const status = memoizedGetStatus(pedido.agendado, pedido.dataAgendada, pedido.horaInicio, pedido.status)
    const distribuidorNome = utf8Decode(pedido.distribuidor.nome)
    const clienteNome = utf8Decode(pedido.cliente.nome)
    const rawOrderDate = pedido.dataPedido || dateToDayMonthYearFormat(pedido.horarioPedido)
    const [_, orderTime] = rawOrderDate.split(" ")

    return {
        ...pedido,
        status,
        horarioPedido: `${checkDate(rawOrderDate)} às ${orderTime}`,
        dataAgendada: getFormatedScheduleDate(pedido),
        distribuidor: {
            ...pedido.distribuidor,
            nome: distribuidorNome.substr(distribuidorNome.indexOf(" ") + 1)
        },
        cliente: {
            ...pedido.cliente,
            nome: clienteNome
        }
    }
}

const transformedOrders = computed(() => {
    return orders.value.map(pedido => transformOrder(pedido))
})

const loadTableData = () => {
    try {
        var urlPedidos = 'pedidos';

        const promise = axios.get(urlPedidos);

        renderToast(promise, 'Atualizando Tabela, aguarde...', 'tabela de pedidos atualizada', (response) => {
            entregadores.value = response.data[7];

            const concatArray = [].concat(
                response.data[0],
                response.data[1],
                response.data[2],
                response.data[3],
                response.data[4]
            );
            const today = new Date();

            const orderMap = new Map(concatArray.map(p => [p.id, p]));
            const scheduledOrders = response.data[5].filter(pedido => {
                if (orderMap.has(pedido.id)) return false;
                const scheduleDate = dateToISOFormat(`${pedido.dataAgendada} ${pedido.horaInicio}`);
                return scheduleDate >= today;
            })
            orders.value = [].concat(concatArray, scheduledOrders);
            data.value = transformedOrders.value;
        })
    } catch (error) {
        toast.error('Erro ao carregar tabela de pedidos')
    }
}

const handleLoadTableData = () => {
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
