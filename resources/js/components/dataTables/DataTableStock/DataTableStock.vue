<script setup>
import { ref, onMounted, computed, watch, onBeforeUnmount } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import 'datatables.net-buttons-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import { StringUtil } from '@/util';
import { tableConfig } from './config/tableConfig';
import { shallowRef } from 'vue';
import renderToast from '@/components/renderPromiseToast';
import { Switch } from '@/components/ui/switch';
import StockQuantityAdjuster from './components/StockQuantityAdjuster.vue';
import { POLLING_INTERVAL } from '@/constants/table';
import ProductStockStatusToggle from './components/ProductStockStatusToggle.vue';
import { cn } from '@/lib/utils';
import { getStockReport } from '@/services/api/stock';

DataTable.use(DataTablesLib);

const props = defineProps({
  selectedDistributorsIds: { type: Array, required: false, default: [] },
  stockResponse: { type: Function, required: false },
  ajustClass: { type: String, required: false },
  isNestedTable: { type: Boolean, required: false },
});

const emit = defineEmits(['update:filteredData']);

const { columns, options } = tableConfig(props.ajustClass, props.isNestedTable);

let dt;
const table = ref();
const data = shallowRef([]);
const stock = ref([]);
const showActiveOnly = ref(false);

const transformStock = (item) => {
  return {
    ...item,
    distribuidor: {
      ...item.distribuidor,
      nome: StringUtil.utf8Decode(item.distribuidor.nome),
    },
    produto: {
      ...item.produto,
      nome: StringUtil.utf8Decode(item.produto.nome),
    },
  };
};

const transformedStock = computed(() => stock.value.map(transformStock));

const loadTableData = (response) => {
  stock.value = response.data;
  data.value = transformedStock.value;
};

const fetchStock = async () => {
  renderToast(
    getStockReport(props.selectedDistributorsIds),
    'Atualizando Tabela, aguarde...',
    'Tabela de estoque atualizada',
    'Erro ao atualizar tabela de estoque',
    loadTableData,
  );
};

const handleLoadTableData = () => {
  props.stockResponse ? loadTableData(props.stockResponse) : fetchStock();
};

const getAllFilteredData = () => {
  const allData = dt.rows({ search: 'applied' }).data().toArray();
  const filteredData = allData.map((row) => ({
    id: row.id,
    distribuidor: row.distribuidor.nome,
    produto: row.produto.nome,
    quantidade: row.quantidade,
    componente: row.produto.componente,
  }));
  emit('update:filteredData', filteredData);
  return filteredData;
};

const extendedOptions = {
  ...options,
  initComplete: function (settings, json) {
    const api = new $.fn.dataTable.Api(settings);

    // Add custom filter
    $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
      if (!showActiveOnly.value) return true;
      const rowData = api.row(dataIndex).data();
      return rowData.produto.status === 1;
    });
  },
};

// Add watch for filter changes
watch(showActiveOnly, () => {
  dt.draw();
});

onMounted(() => {
  dt = table.value.dt;

  dt.on('draw', function () {
    const container = dt.table().container();
    container.querySelectorAll('.dt-paging-button').forEach((btn) => {
      btn.removeEventListener('pointerdown', paginationHandler);
      btn.addEventListener('pointerdown', paginationHandler, true);
    });
  });

  function paginationHandler(e) {
    if (e.target.closest('.dt-paging-button')) {
      e.stopPropagation();
      e.preventDefault();
      console.log('Pagination pointerdown intercepted on draw');
    }
  }
  const topClasses = 'block';
  const selectClasses = '!bg-info !text-white font-bold !border-info !rounded-md ring-info/40';
  const optionClasses = '!bg-white !text-info !border-info';
  const searchClasses =
    'flex items-center py-2 px-1 gap-2 !text-info/80 !mb-[30px] min-[768px]:!mb-[10px] relative -top-11 max-w-60 mx-auto';
  const inputClasses =
    'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary !border-input placeholder:text-info/50 !text-info/80';
  // const bottomClasses =
  //   '!bg-info !text-white flex justify-between py-0.5 px-2 items-center font-bold !border-info !rounded-b-md ring-info/40';

  $('.top').addClass(topClasses);
  $('.dt-length>select').addClass(selectClasses);
  $('.dt-length>select>option').addClass(optionClasses);
  $('.dt-search').addClass(searchClasses);
  $('.dt-search > input').addClass(inputClasses);
  $('.dt-search > label').html(/*html*/ `
    <span class="hidden">pesquisar</span>
    <i class="ri-search-2-fill"></i>
    `);
  // $('.bottom').addClass(bottomClasses);

  handleLoadTableData();

  props.isNestedTable && dt.on('search.dt', () => getAllFilteredData());

  const observeStockChanges = (callback) => {
    return () => {
      callback();
    };
  };

  window.setInterval(observeStockChanges(handleLoadTableData), POLLING_INTERVAL);
  // Stop click propagation on pagination buttons
  // Correct event delegation for dynamically added pagination buttons
  const dialogContent = document.getElementById('radix-vue-dialog-content-v-68');
  if (dialogContent) {
    dialogContent.addEventListener(
      'pointerdown',
      (e) => {
        if (e.target.closest('.dt-paging-button')) {
          // Stop the event as early as possible
          e.stopPropagation();
          // Optionally also stop default if needed
          e.preventDefault();
          console.log('Pagination pointerdown intercepted');
        }
      },
      true, // Use capturing phase
    );
  }
});

onBeforeUnmount(() => {
  $(document).off('click', '.dt-paging-button');
});
</script>

<template>
  <div>
    <div class="mb-4 flex items-center gap-2">
      <Switch
        v-model="showActiveOnly"
        class="data-[state=checked]:bg-success data-[state=unchecked]:bg-info/40"
        @update:checked="(val) => (showActiveOnly = val)"
      />
      <span :class="cn(['text-sm font-medium', showActiveOnly ? 'text-success' : 'text-info/40'])">
        {{ showActiveOnly ? 'Mostrando produtos ativos' : 'Mostrando todos os produtos' }}
      </span>
    </div>
    <DataTable
      ref="table"
      class="display [&_thead]:bg-info [&_thead]:text-[#F3F9FD] [&_td>div.flex>span.hidden]:!block"
      :columns="columns"
      :data="data"
      :options="extendedOptions"
      language="language"
    >
      <template #action="data">
        <StockQuantityAdjuster
          :stockData="data.rowData"
          :loadTable="fetchStock"
          :isNestedTable="isNestedTable"
        />
      </template>
      <template #status="data">
        <ProductStockStatusToggle :stockData="data.rowData" :loadTable="fetchStock" />
      </template>
    </DataTable>
  </div>
</template>

<style>
@import 'datatables.net-dt';
</style>
