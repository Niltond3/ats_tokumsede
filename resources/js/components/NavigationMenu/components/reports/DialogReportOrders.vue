<script setup>
import { ref, computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import DataTablePedidos from '@/Pages/Management/DataTablePedidos/DataTablePedidos.vue';
import { Form } from '@/components/ui/form';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { Button } from '@/components/ui/button';
import { listAllDistributors } from '@/services/api/distributors';
import renderToast from '@/components/renderPromiseToast';
import { cn, utf8Decode } from '@/util';
import DistributorCombobox from './DistributorCombobox.vue';
import { jsPDF } from 'jspdf';
import 'jspdf-autotable';

const isOpen = ref(false);
const dateRange = ref(undefined);
const hasDateError = ref(false);
const orderResponse = ref(null);
const searchTerm = ref('');
const selectedDistributors = ref([]);
const distributors = ref([]);
const isLoading = ref(true);
const isLoadingReport = ref(false);
const filteredData = ref([]);
const showReportButton = computed(() => filteredData.value.length > 0);

watch(
  selectedDistributors,
  () => {
    searchTerm.value = '';
  },
  { deep: true },
);

const createDateRange = (daysBack) => {
  const end = new Date();
  const start = new Date();
  start.setDate(start.getDate() - daysBack);
  start.setHours(0, 0, 0, 0);
  end.setHours(23, 59, 59, 999);
  return [start, end];
};

const generateReport = () => {
  const doc = new jsPDF();

  const distribuidorData = filteredData.value.reduce((acc, pedido) => {
    if (!acc[pedido.distribuidor]) {
      acc[pedido.distribuidor] = {
        clientes: {},
        totalPedidos: 0,
        valorTotal: 0,
      };
    }

    if (!acc[pedido.distribuidor].clientes[pedido.cliente]) {
      acc[pedido.distribuidor].clientes[pedido.cliente] = {
        pedidos: 0,

        valorTotal: 0,
        produtos: {},
      };
    }

    // Track products for each client
    pedido.itens.forEach((item) => {
      if (!acc[pedido.distribuidor].clientes[pedido.cliente].produtos[item.nome]) {
        acc[pedido.distribuidor].clientes[pedido.cliente].produtos[item.nome] = {
          quantidade: 0,
          valorUnitario: item.valorUnitario,
        };
      }
      acc[pedido.distribuidor].clientes[pedido.cliente].produtos[item.nome].quantidade +=
        item.quantidade;
    });

    acc[pedido.distribuidor].clientes[pedido.cliente].pedidos++;
    acc[pedido.distribuidor].clientes[pedido.cliente].valorTotal += pedido.total;
    acc[pedido.distribuidor].totalPedidos++;
    acc[pedido.distribuidor].valorTotal += pedido.total;

    return acc;
  }, {});

  doc.setFont('helvetica', 'bold');
  doc.text('Relatório Detalhado de Pedidos por Distribuidor', 14, 20);

  let yPos = 40;

  Object.entries(distribuidorData).forEach(([distribuidor, data]) => {
    doc.setFont('helvetica', 'bold');
    doc.text(`Distribuidor: ${distribuidor}`, 14, yPos);
    doc.setFont('helvetica', 'normal');
    doc.text(`Total de Pedidos: ${data.totalPedidos}`, 14, yPos + 10);
    doc.text(`Valor Total: R$ ${data.valorTotal.toFixed(2)}`, 14, yPos + 20);

    yPos += 35;

    const headers = [['Cliente', 'Qtd. Pedidos', 'Valor Total', 'Produtos (Qtd x Valor Unit.)']];
    const clientData = Object.entries(data.clientes).map(([cliente, info]) => {
      const produtosStr = Object.entries(info.produtos)
        .map(([nome, dados]) => `${nome}: ${dados.quantidade}x R${dados.valorUnitario.toFixed(2)}`)
        .join('\n');

      return [cliente, info.pedidos.toString(), `R$ ${info.valorTotal.toFixed(2)}`, produtosStr];
    });

    doc.autoTable({
      startY: yPos,
      head: headers,
      body: clientData,
      margin: { left: 14 },

      theme: 'grid',
      columnStyles: {
        3: { cellWidth: 80 },
      },
    });

    yPos = doc.lastAutoTable.finalY + 30;

    if (yPos > 250) {
      doc.addPage();
      yPos = 20;
    }
  });

  doc.save('relatorio-pedidos-detalhado.pdf');
};
const presets = computed(() => [
  {
    label: 'Último dia',
    value: createDateRange(1),
  },
  {
    label: 'Última semana',
    value: createDateRange(7),
  },
  {
    label: 'Último mês',
    value: createDateRange(30),
  },
  {
    label: 'Último ano',
    value: createDateRange(365),
  },
]);

async function getDistributors() {
  isLoading.value = true;
  renderToast(
    listAllDistributors(),
    'Carregando distribuidores...',
    'Distribuidores carregados com sucesso!',
    'Erro ao carregar distribuidores',
    (response) => {
      distributors.value = response.data.data.map((d) => ({ id: d.id, nome: utf8Decode(d.nome) }));
      isLoading.value = false;
    },
  );
}

async function fetchOrdersReport() {
  if (!dateRange.value) {
    hasDateError.value = true;
    toast.warning('Selecione um período para gerar o relatório');
    return;
  }
  isLoadingReport.value = true;
  hasDateError.value = false;
  const [startDate, endDate] = dateRange.value;
  const distributorIds = selectedDistributors.value
    .map((name) => distributors.value.find((d) => d.nome === name)?.id)
    .filter((id) => id) // Remove falsy values
    .join(',');

  const promise = axios.post('/relatorio/pedidos', {
    dataInicial: startDate?.toLocaleDateString('pt-BR'),
    dataFinal: endDate?.toLocaleDateString('pt-BR'),
    idDistribuidores: distributorIds || null, // Send null if no distributors selected
  });

  renderToast(
    promise,
    'Carregando relatório...',
    'Relatório gerado com sucesso!',
    'Erro ao gerar relatório',
    (response) => {
      orderResponse.value = response;
      isLoadingReport.value = false;
    },
  );
}

watch(dateRange, () => {
  if (dateRange.value) {
    hasDateError.value = false;
  }
});

const resetValues = () => {
  // Reset all ref values to initial state
  dateRange.value = undefined;
  hasDateError.value = false;
  orderResponse.value = null;
  searchTerm.value = '';
  selectedDistributors.value = [];
  filteredData.value = [];
  isLoadingReport.value = false;
};

getDistributors();
</script>

<template>
  <Dialog v-model:open="isOpen" :modal="true">
    <slot name="trigger" @click="isOpen = true" />
    <DialogContent
      class="!z-[50] max-w-[90vw] sm:max-w-3xl"
      @close.prevent
      @pointerDownOutside.prevent
      :class="{
        'overflow-auto px-2 overflow-x-hidden': orderResponse,
        'overflow-visible': !orderResponse,
      }"
    >
      <DialogHeader class="flex justify-between items-center mb-3">
        <div class="flex items-center gap-4">
          <DialogTitle class="text-info">Relatório de Pedidos</DialogTitle>
          <Button
            v-if="showReportButton"
            @click="generateReport"
            variant="outline"
            size="sm"
            class="text-info/40 hover:text-info transi"
          >
            <i class="ri-file-list-3-line mr-2" />
            Baixar Relatório
          </Button>
        </div>
        <Button
          v-if="orderResponse"
          @click="resetValues()"
          variant="ghost"
          size="sm"
          :class="
            cn([
              'group h-4 p-0 flex justify-start rounded-sm absolute top-4 font-thin opacity-70 transition-all',
              'max-[640px]:left-4',
              'sm:w-6 sm:right-6',
              'sm:hover:w-24 sm:hover:h-8 sm:hover:top-2 sm:hover:bg-info sm:hover:text-white sm:hover:font-bold sm:hover:px-2 sm:hover:right-8 hover:text-info hover:opacity-100',
            ])
          "
        >
          <i class="ri-arrow-left-line" />
          <span
            :class="
              cn([
                'max-[640px]:sr-only',
                'sm:pointer-events-none sm:opacity-0 sm:select-none sm:transition-opacity',
                'sm:group-hover:opacity-100',
              ])
            "
            >Voltar</span
          >
        </Button>
      </DialogHeader>
      <div v-if="!orderResponse" class="mb-4">
        <Form @submit="fetchOrdersReport">
          <div class="flex flex-col sm:flex-row gap-2">
            <div class="sm:w-1/2">
              <VueDatePicker
                v-model="dateRange"
                range
                locale="pt-BR"
                format="dd/MM/yyyy"
                :enable-time-picker="false"
                :preset-dates="presets"
                clearable
                auto-apply
                :class="[
                  { 'border-red-500 ring-red-500': hasDateError },
                  '[&_input]:h-14', // Match height with DistributorCombobox
                ]"
              />
            </div>
            <div class="sm:w-1/2">
              <DistributorCombobox
                v-model="selectedDistributors"
                v-model:search-term="searchTerm"
                :distributors="distributors"
                :is-loading="isLoading"
                :disabled="isLoadingReport"
              />
            </div>
          </div>

          <Button type="submit" class="mt-4 disabled:cursor-not-allowed" :disabled="isLoadingReport"
            >Gerar Relatório</Button
          >
        </Form>
      </div>
      <!-- Add DataTable section -->
      <div v-else class="flex flex-col h-[80vh]" @click.stop>
        <div class="nested-dialog-context" @click.stop>
          <DataTablePedidos
            @update:filteredData="(data) => (filteredData = data)"
            :orderResponse="orderResponse"
            ajustClass="!top-[100px] md:!top-[90px]"
            :isNestedTable="true"
          />
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
