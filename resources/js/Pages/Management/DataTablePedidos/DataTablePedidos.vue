<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import 'datatables.net-buttons-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import { utf8Decode, dateToDayMonthYearFormat, dateToISOFormat } from '@/util';
import { getStatusString } from '../utils';
import DropDownPedidos from './components/DropDownPedidos.vue';
import ActionOrders from './components/ActionOrders.vue';
import observeNewOrders from './components/observeNewOrders';
import renderToast from '@/components/renderPromiseToast';
import { tableConfig } from './config/tableConfig';
import { shallowRef } from 'vue';
import { useMemoize } from '@vueuse/core';
import { getOrder } from '@/services/api/orders';
import { POLLING_INTERVAL } from './config/constants';

DataTable.use(DataTablesLib);

const props = defineProps({
  orderResponse: { type: Function, required: false },
  setTab: { type: Function, required: false },
  ajustClass: { type: String, required: false },
  isNestedTable: { type: Boolean, required: false },
});

const emit = defineEmits(['update:filteredData']);

const { columns, options } = tableConfig(props.ajustClass, props.isNestedTable);

let dt;
const table = ref();
const data = shallowRef([]);
const orders = ref([]);
const scheduleOrder = ref([]);
const entregadores = ref([]);

const getFormatedScheduleDate = (dataAgendada, horaInicio) => {
  if (!dataAgendada) return dataAgendada;
  const rawScheduleDate = `${dataAgendada} ${horaInicio}`;
  const { dateTime } = dateToDayMonthYearFormat(rawScheduleDate);
  return dateTime;
};

const transformOrder = (pedido) => {
  const status = getStatusString(
    pedido.agendado,
    pedido.dataAgendada,
    pedido.horaInicio,
    pedido.status,
  );
  const distribuidorNome = utf8Decode(pedido.distribuidor.nome);
  const clienteNome = utf8Decode(pedido.cliente.nome);
  const rawOrderDate = dateToDayMonthYearFormat(pedido.horarioPedido);
  const { dateTime } = rawOrderDate;
  const order = {
    ...pedido,
    status,
    horarioPedido: dateTime,
    dataAgendada: getFormatedScheduleDate(pedido.dataAgendada, pedido.horaInicio),
    distribuidor: {
      ...pedido.distribuidor,
      nome: distribuidorNome.substr(distribuidorNome.indexOf(' ') + 1),
    },
    cliente: {
      ...pedido.cliente,
      nome: clienteNome,
    },
  };
  return order;
};

const transformedOrders = computed(() => orders.value.map((pedido) => transformOrder(pedido)));

const loadTableData = (response) => {
  entregadores.value = response.data[7];
  scheduleOrder.value = response.data[5]; // agendados
  const concatArray = [].concat(
    response.data[0], // pendentes
    response.data[1], // aceitos
    response.data[2], // despachados
    response.data[3], // entregues
    response.data[4], // cancelados
    response.data[5], // agendados
  );
  orders.value = concatArray;
  data.value = transformedOrders.value;
  console.log(data.value);
  console.log(scheduleOrder.value);
};

const fetchOrders = async () => {
  renderToast(
    getOrder(),
    'Atualizando Tabela, aguarde...',
    'tabela de pedidos atualizada',
    'Erro ao atualizar tabela de pedidos',
    loadTableData,
  );
};

const handleLoadTableData = () => {
  props.orderResponse ? loadTableData(props.orderResponse) : fetchOrders();
};

const getAllFilteredData = () => {
  const allData = dt.rows({ search: 'applied' }).data().toArray();
  const filteredData = allData.map((row) => {
    return {
      ...row,
      status: row.status.label,
      distribuidor: row.distribuidor.nome,
      cliente: row.cliente.nome,
      clienteTelefone: row.cliente.dddTelefone + row.cliente.telefone,
      dataAgendada: row.dataAgendada,
      horarioPedido: row.horarioPedido,
      endereco: {
        estado: row.endereco.estado,
        cidade: row.endereco.cidade,
        bairro: row.endereco.bairro,
        logradouro: row.endereco.logradouro,
        numero: row.endereco.numero,
        referencia: row.endereco.referencia,
        complemento: row.endereco.complemento,
        observacao: row.endereco.observacao,
      },
      itens: row.itens.map((item) => ({
        nome: item.produto.nome,
        quantidade: item.qtd,
        valorUnitario: item.preco,
      })),
    };
  });
  emit('update:filteredData', filteredData);
  return filteredData;
};

onMounted(() => {
  dt = table.value.dt;
  $('.dt-search').addClass(
    'flex items-center py-2 px-1 gap-2 !text-info/80 !mb-[30px] min-[768px]:!mb-[10px]',
  );

  $('.dt-search > label').html(/*html*/ `
    <span class="hidden">pesquisar</span>
    <i class="ri-search-2-fill"></i>
    `);

  const inputClasses =
    'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary !border-input placeholder:text-info/50 !text-info/80';

  $('.dt-search > input').addClass(inputClasses);

  handleLoadTableData();

  props.isNestedTable && dt.on('search.dt', () => getAllFilteredData());
  const getScheduleOrder = () => {
    return orders.value.filter((order) => {
      const dateIso = dateToISOFormat(`${order.dataAgendada} ${order.horaInicio}`);
      const currentDate = new Date();
      const scheduleDate = new Date(dateIso);

      const timeDiff = (scheduleDate - currentDate) / (1000 * 60);

      if (currentDate < scheduleDate && timeDiff > 30) {
        return order;
      }
    });
  };
  window.setInterval(async () => {
    await observeNewOrders(handleLoadTableData);
    const currentScheduleOrders = getScheduleOrder();
    const isEqual = JSON.stringify(scheduleOrder.value) === JSON.stringify(currentScheduleOrders);
    console.log(isEqual);
    if (!isEqual) {
      await fetchOrders();
    }
  }, POLLING_INTERVAL);
});

const badgeClasses =
  'font-normal inline-block px-2 text-[75%] text-center whitespace-nowrap align-baseline rounded text-white';
</script>

<template>
  <DataTable
    ref="table"
    class="display [&_thead]:bg-info [&_thead]:text-[#F3F9FD] [&_td>div.flex>span.hidden]:!block"
    :columns="columns"
    :data="data"
    :options="options"
    language="language"
  >
    <template #action="data">
      <div>
        <ActionOrders
          :payloadData="data.rowData"
          :entregadores="entregadores"
          :loadTable="handleLoadTableData"
          :isNestedTable="isNestedTable"
        />
        <DropDownPedidos
          :payloadData="data.rowData"
          :entregadores="entregadores"
          :loadTable="fetchOrders"
          :isNestedTable="isNestedTable"
          @callback:edited-order="handleLoadTableData"
        />
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
