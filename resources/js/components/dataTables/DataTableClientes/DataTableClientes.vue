<script setup>
// Vue and Core imports
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import { useWindowSize } from '@vueuse/core';

// DataTables Core and Extensions
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

// Utility imports
import { ErrorUtil, StringUtil } from '@/util';
import { dialogState } from '@/composables/useToggleDialog';
import languagePtBR from './dataTablePtBR.mjs';
import { OrderUtil } from '@/util';
import rowChildtable from './components/rowChildTable/index';
import DialogShowOrder from '@/components/dialogs/DialogShowOrder.vue';
import DialogCreateOrder from '@/components/dialogs/DialogCreateOrder.vue';
import DialogRegisterAddress from '@/components/dialogs/DialogRegisterAddress.vue';
import DialogConfirmAddressDelete from '@/components/dialogs/DialogConfirmAddressDelete.vue';
import DialogRegisterClient from '@/components/dialogs/DialogRegisterClient.vue';
import { DialogRegisterPrices } from '@/components/dialogs/DialogRegisterPrices';
import renderToast from '@/components/renderPromiseToast';
import DialogEditOrder from '@/components/dataTables/DataTablePedidos/components/DialogEditOrder.vue';
import ClientActionsWrapper from '@/components/wrapper/ClientActionsWrapper.vue';
import ClientActionsDropdown from '@/components/dropdowns/ClientActionsDropdown.vue';
import { updateCoordinates } from '@/services/api/addresses';
import { createClient, getClient } from '@/services/api/client';

DataTable.use(DataTablesCore);

const props = defineProps({
  setTab: { type: Function, required: true },
});

const { isOpen: isOpenCreateOrderDialog, toggleDialog: toggleCreateOrderDialog } =
  dialogState('CreateOrder');
const { isOpen: openShowOrderDialog, toggleDialog: toggleShowOrderDialog } =
  dialogState('ShowOrder');
const { isOpen: openEditOrderDialog, toggleDialog: toggleEditOrderDialog } =
  dialogState('EditOrder');
const { isOpen: openRegisterAddress, toggleDialog: toggleRegisterAddress } =
  dialogState('RegisterAddres');
const { isOpen: openEditAddress, toggleDialog: toggleEditAddress } = dialogState('RegisterAddress');
const { isOpen: openConfirmDialog, toggleDialog: toggleConfirmDialog } =
  dialogState('ConfirmAddressDelete');
const { isOpen: openRegisterPrices, toggleDialog: toggleRegisterPrices } =
  dialogState('RegisterPrices');

const { width } = useWindowSize();

const idClienteAddress = ref('');
const idClient = ref('');
const reminders = ref([]);
const clientName = ref('');
const idAddress = ref('');
const idOrder = ref(0);
const address = ref({});
const addressTarget = ref({});
const clientCache = new Map();

let dt;
const table = ref();

const initializeDataTable = (dt) => {
  const format = (d) => rowChildtable(d);

  setupChildRowHandler(dt, format);
  setupSearchStyling();
  setupEventHandlers(dt);
};

const dragScrollList = (elementId) => {
  const ele = document.getElementById(elementId);
  if (!ele) return;
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
};

const handleUpdateDataTable = () => dt.ajax.reload();

const setupChildRowHandler = (dt, format) => {
  $('#datatable-clientes tbody').on('click', 'td.dt-control', async function () {
    const tr = $(this).closest('tr');
    const row = dt.row(tr);
    const client = row.data();

    if (row.child.isShown()) {
      addressTarget.value == null && handleUpdateDataTable();
      return row.child.hide();
    }

    try {
      let clientData;
      if (clientCache.has(client.id)) {
        clientData = clientCache.get(client.id);
      } else {
        await renderToast(
          getClient(client.id),
          'Carregando Cliente...',
          'Cliente carregado',
          'Falha ao carregar cliente',
          (response) => {
            clientData = response.data;
            reminders.value = response.data.reminders;
            clientCache.set(client.id, clientData);
          },
        );
      }

      const pedidos = clientData.pedidos.map((pedido) => OrderUtil.formatOrder(pedido));
      const childData = { ...client, pedidos };
      row.child(format(childData)).show();
    } finally {
      dragScrollList('enderecos');
      dragScrollList('pedidos');
    }
  });
};

const setupSearchStyling = () => {
  $('.dt-search').addClass('flex items-center py-2 px-4 gap-2 !text-info/80');
  $('.dt-search > label').html(/*html*/ `
        <span class="hidden">pesquisar</span>
        <i class="ri-search-2-fill"></i>
    `);

  const inputClasses =
    'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary !border-input placeholder:text-info/50 !text-info/80';
  $('.dt-search > input').addClass(inputClasses);
};

const setupEventHandlers = (dt) => {
  setupOrderHandlers();
  setupAddressHandlers(dt);
  setupCopyHandler(dt);
  setupAccordionHandler();
};

const setupOrderHandlers = () => {
  $('#datatable-clientes').on('click', '.iniciarPedido', function () {
    clientName.value = this.getAttribute('data-client');
    idClienteAddress.value = this.id;
    toggleCreateOrderDialog();
  });

  $('#datatable-clientes').on('click', '.visualizarPedido', function () {
    idOrder.value = parseInt(this.id);
    toggleShowOrderDialog();
  });

  $('#datatable-clientes').on('click', '.editOrder', function () {
    idOrder.value = parseInt(this.getAttribute('order_id'));
    toggleEditOrderDialog();
  });
};

const setupAddressHandlers = (dt) => {
  $('#datatable-clientes').on('click', '.editarEndereco', function () {
    const idAddress = this.getAttribute('addr_id');
    const idClient = this.getAttribute('cli_id');
    const data = dt.data();
    address.value = Object.values(data)
      .filter((cli) => cli.id == idClient)[0]
      .enderecos.filter((addr) => addr.id == idAddress)[0];
    toggleEditAddress();
  });

  $('#datatable-clientes').on('click', '.novoEndereco', function () {
    idClient.value = this.id;
    console.log(this.id);
    toggleRegisterAddress();
  });

  $('#datatable-clientes').on('click', '.novoPrecoEspecial', (event) => {
    console.log(event.target);
    console.log(event.target.id);
    idClient.value = event.target.id;
    toggleRegisterPrices();
  });
  $('#datatable-clientes').on('click', '.excluirEndereco', toggleConfirmDialog);

  //
  $('#datatable-clientes').on('click', '.atualizarCoordenadas', (event) => {
    //idAddress
    renderToast(
      updateCoordinates(idAddress.value),
      'Atualizando coordenadas',
      'Coordenadas atualizadas',
      'Falha ao atualizar coordenadas',
    );
  });
  $('#datatable-clientes').on('long-press', '.selectOrder', function (e) {
    idClient.value = e.target.getAttribute('client_id');
    idOrder.value = e.target.getAttribute('order_id');
    e.target.setAttribute(
      'aria-selected',
      e.target.getAttribute('aria-selected') === 'true' ? 'false' : 'true',
    );
    addressTarget.value = e.target;
    e.target.parentNode.classList.toggle("[&>li[aria-selected='false']]:pointer-events-none");
  });

  $('#datatable-clientes').on('long-press', '.deleteEndereco', function (e) {
    idClient.value = e.target.id;
    idAddress.value = e.target.getAttribute('addr_id');
    e.target.setAttribute(
      'aria-selected',
      e.target.getAttribute('aria-selected') === 'true' ? 'false' : 'true',
    );
    addressTarget.value = e.target;
    e.target.parentNode.classList.toggle("[&>li[aria-selected='false']]:pointer-events-none");
  });
};

const setupCopyHandler = (dt) => {
  $('#datatable-clientes').on('click', '.copiarEndereco', function () {
    const data = dt.data();
    const payload = Object.values(data)
      .filter((cli) => cli.id == idClient.value)[0]
      .enderecos.filter((addr) => addr.id == idAddress.value)[0];

    const clipboard = formatAddressForClipboard(payload);
    navigator.clipboard.writeText(clipboard);
    toast.info('Copiado para a área de transferência');
  });
};

const setupAccordionHandler = () => {
  $('#datatable-clientes').on('click', '.accordionController', function (e) {
    e.target.classList.toggle('after:!content-["-"]');
    const panel = e.target.parentNode.nextElementSibling;
    panel.firstChild.nextElementSibling.classList.toggle('!max-h-[11rem]');
  });
};

const formatAddressForClipboard = (address) => {
  return `
------------- endereço -------------
${address.cep ? 'CEP: ' + address.cep : ''}
Cidade: ${address.cidade}
${address.estado !== null ? 'Estado: ' + address.estado : ''}
${address.apelido ? 'Apelido: ' + address.apelido : ''}
Logradouro: ${address.logradouro}
Número: ${address.numero}
Bairro: ${address.bairro}
${address.complemento ? 'Complemento: ' + address.complemento : ''}
${address.referencia ? 'Referência: ' + address.referencia : ''}
`.replace(/(^[ \t]*\n)/gm, '');
};

onMounted(() => {
  dt = table.value.dt;
  initializeDataTable(dt);
  //   window.setInterval(async () => {
  //     await observeNewOrders;
  //   }, 10000);
});

const columns = [
  {
    className: 'dt-control all',
    orderable: false,
    data: null,
    defaultContent: '',
    render: '#dt-control',
    responsivePriority: 1,
  },
  { data: 'nome', title: 'Nome', responsivePriority: 1, className: 'all' },
  { data: 'tipoPessoa', title: 'CPF/CNPJ', searchable: false, responsivePriority: 6 },
  { data: 'telefone', title: 'Telefone', responsivePriority: 3 },
  { data: 'outrosContatos', title: 'Outros Contatos', responsivePriority: 4 },
  { data: 'rating', title: 'Rating', searchable: false, responsivePriority: 5 },
  {
    data: 'opcoes',
    title: 'Opções',
    render: '#actions',
    searchable: false,
    responsivePriority: 1,
    className: 'all',
  },
  { data: 'enderecos[].logradouro', name: 'enderecos.logradouro', visible: false },
  { data: 'enderecos[].bairro', name: 'enderecos.bairro', visible: false },
  { data: 'enderecos[].numero', name: 'enderecos.numero', visible: false },
  { data: 'enderecos[].cidade', name: 'enderecos.cidade', visible: false },
  { data: 'enderecos[].estado', name: 'enderecos.estado', visible: false },
  { data: 'dddTelefone', visible: false },
];

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
      { name: 'mobilep', width: 320 },
    ],
  },
  layout: {
    top: 'search',
    topStart: null,
    topEnd: null,
    bottomStart: 'info',
    bottomEnd: 'paging',
  },
  initComplete: function (settings, json) {
    const table = this.api();

    table.on('search.dt', async function () {
      const searchValue = table.search();
      if (searchValue) {
        try {
          const dtParams = table.ajax.params();
          const response = await createClient;
          const fullData = response.data.data.map((client) => ({
            ...client,
            nome: StringUtil.utf8Decode(client.nome),
            enderecos: client.enderecos.map((address) => ({
              ...address,
              logradouro: StringUtil.utf8Decode(address.logradouro || ''),
              bairro: StringUtil.utf8Decode(address.bairro || ''),
              cidade: StringUtil.utf8Decode(address.cidade || ''),
              observacao: StringUtil.utf8Decode(address.observacao || ''),
              referencia: StringUtil.utf8Decode(address.referencia || ''),
              apelido: StringUtil.utf8Decode(address.apelido || ''),
            })),
          }));
        } catch (error) {
          console.error('Error fetching complete dataset:', error);
        }
      }
    });
  },
};
const ajax = {
  url: 'clientes',
  dataFilter: function (data) {
    const obj = JSON.parse(data);
    const newData = obj.data.map((client) => {
      const nome = StringUtil.utf8Decode(client.nome);
      const enderecos = client.enderecos.map((address) => {
        return {
          ...address,
          logradouro: StringUtil.utf8Decode(address.logradouro || ''),
          bairro: StringUtil.utf8Decode(address.bairro || ''),
          cidade: StringUtil.utf8Decode(address.cidade || ''),
          observacao: StringUtil.utf8Decode(address.observacao || ''),
          referencia: StringUtil.utf8Decode(address.referencia || ''),
          apelido: StringUtil.utf8Decode(address.apelido || ''),
        };
      });

      const newClient = { ...client, nome, enderecos };

      return newClient;
    });
    const newObj = { ...obj, data: newData };

    return JSON.stringify(newObj);
  },
  error: function (err) {
    console.error(ErrorUtil.getError(err));
    toast.error('Ocorreu um erro ao carregar os clientes!' + ErrorUtil.getError(err));
  },
};

const handleDeleteAddress = (confirm) => {
  if (confirm === false) return toggleConfirmDialog();

  let container = addressTarget.value;

  container.classList.add('w-0');
  container.classList.add('opacity-0');
  container.classList.add('hidden');

  container.parentNode.classList.toggle("[&>li[aria-selected='false']]:pointer-events-none");
  addressTarget.value = null;
  toggleConfirmDialog();
  container.ontransitionend = function () {};
};
</script>

<template>
  <div
    class="[&_.dt-search]:relative [&_.dt-search>label]:ri-search-2-fill [&_.dt-paging]:bg-info [&_.dt-paging]:rounded-md [&_.dt-paging]:font-bold [&_.dt-paging]:!text-white [&_.dt-layout-row]:flex [&_.dt-layout-row]:justify-between [&_.dt-layout-row]:items-center [&_.dt-info]:text-sm [&_.dt-info]:!text-info [&_.dt-info]:font-bold [&_.dt-paging]:flex [&_.dt-paging]:gap-1 [&_.dt-paging-button]:px-3 [&_.dt-paging-button]:py-1 [&_.dt-paging-button]:rounded [&_.dt-paging-button]:transition-colors [&_.dt-paging-button]:text-info [&_.dt-paging-button.disabled]:opacity-50 [&_.dt-paging-button.disabled]:cursor-not-allowed [&_.dt-paging-button.current]:bg-info [&_.dt-paging-button.current]:text-white [&_.dt-paging-button]:hover:bg-info/10 [&_.ellipsis]:px-2 [&_.ellipsis]:text-gray-500"
  >
    <DialogRegisterPrices
      :addressId="idAddress"
      :clientId="idClient"
      :isOpen="openRegisterPrices"
      :toggleDialog="toggleRegisterPrices"
      title="Cadastro de preços especiais"
    />
    <DialogCreateOrder
      :open="isOpenCreateOrderDialog"
      :toggleDialog="toggleCreateOrderDialog"
      :reminders="reminders"
      :id-cliente-address="idClienteAddress"
      :client-name="clientName"
      :set-tab="props.setTab"
      @update:data-table="handleUpdateDataTable"
    />
    <DialogShowOrder
      :open="openShowOrderDialog"
      :reminders="reminders"
      :toggleDialog="toggleShowOrderDialog"
      :order-id="idOrder"
    />
    <DialogRegisterClient @update:data-table="handleUpdateDataTable" />
    <DialogRegisterAddress
      :open="openRegisterAddress"
      :toggleDialog="toggleRegisterAddress"
      :id-client="idClient"
      @update:data-table="handleUpdateDataTable"
    />
    <DialogRegisterAddress
      :open="openEditAddress"
      :toggleDialog="toggleEditAddress"
      :address-details="address"
      @update:data-table="handleUpdateDataTable"
    />
    <DialogConfirmAddressDelete
      dialog-title="Excluir Endereço"
      variant="danger"
      :id-address="idAddress"
      dialog-description="Tem certeza que deseja excluir o endereço?"
      :open="openConfirmDialog"
      @delete:confirm="handleDeleteAddress"
    />
    <DialogEditOrder v-model="openEditOrderDialog" :order-id="idOrder" :controlled="true">
      <template #trigger>
        <button class="sr-only" @click="openEditOrderDialog = true">Custom Open Dialog</button>
      </template>
    </DialogEditOrder>
    <DataTable
      id="datatable-clientes"
      ref="table"
      class="display [&_thead]:bg-info [&_thead]:!rounded-t-md [&_thead]:text-[#F3F9FD] [&_tbody>tr>td.dt-control]:before:hidden [&_tbody>tr.dt-hasChild_span.transition-all]:rotate-90 [&_tbody>tr.dt-hasChild_div.relative]:bg-danger"
      :columns="columns"
      :ajax="ajax"
      :options="options"
      language="language"
    >
      <template #actions="data">
        <div class="max-w-3/4 flex items-center justify-center">
          <ClientActionsDropdown v-if="width >= 768" :payload-data="data" :data-table="dt" />
          <ClientActionsWrapper v-else :payloadData="data" :data-table="dt" />
        </div>
      </template>
      <template #dt-control>
        <div class="relative group m-auto w-[14px] h-[14px] bg-info rounded-full p-2">
          <span
            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 block w-[2px] h-[10px] bg-white transition-all"
          ></span>
          <span
            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 block h-px w-[10px] bg-white"
          ></span>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<style>
@import 'datatables.net-dt';
</style>
