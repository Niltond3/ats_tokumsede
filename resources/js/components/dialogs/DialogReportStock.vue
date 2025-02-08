<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import DataTablePedidos from '@/components/dataTables/DataTablePedidos/DataTablePedidos.vue';
import { Form } from '@/components/ui/form';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { Button } from '@/components/ui/button';
import { getDistributor, listAllDistributors } from '@/services/api/distributors';
import renderToast from '@/components/renderPromiseToast';
import { StringUtil } from '@/util';
import { cn } from '@/lib/utils';
import DistributorCombobox from './DialogStockMerge/DistributorCombobox.vue';
import { jsPDF } from 'jspdf';
import { Skeleton } from '@/components/ui/skeleton';
import 'jspdf-autotable';
import DataTableStock from '@/components/dataTables/DataTableStock/DataTableStock.vue';
import { getStockReport } from '@/services/api/stock';

const props = defineProps({
  isOpen: { type: Boolean, required: false, default: null },
  toggleDialog: { type: Function, required: false, default: null },
});

const stockResponse = ref(null);
const searchTerm = ref('');
const selectedDistributors = ref([]);
const selectedDistributorIds = ref([]);
const distributors = ref([]);
const distributorName = ref(null);
const isLoading = ref(true);
const isLoadingReport = ref(false);
const filteredData = ref([]);

const page = usePage();

const { tipoAdministrador, nome: administratorName } = page.props.auth.user;

async function fetchStockReport() {
  isLoadingReport.value = true;

  selectedDistributorIds.value = selectedDistributors.value
    .map((name) => distributors.value.find((d) => d.nome === name)?.id)
    .filter((id) => id)
    .join(',');

  console.log(distributors.value);
  console.log(selectedDistributors.value);
  console.log(selectedDistributorIds.value);

  renderToast(
    getStockReport(selectedDistributorIds.value),
    'Carregando relatório...',
    'Relatório gerado com sucesso!',
    'Erro ao gerar relatório',
    (response) => {
      console.log(response);
      stockResponse.value = response;
      isLoadingReport.value = false;
    },
  );
}

watch(
  selectedDistributors,
  () => {
    searchTerm.value = '';
  },
  { deep: true },
);

const filteredDistributors = computed(() => {
  if (!searchTerm.value || tipoAdministrador === 'Distribuidor') {
    return distributors.value;
  }
  return distributors.value.filter((d) =>
    d.nome.toLowerCase().includes(searchTerm.value.toLowerCase()),
  );
});

async function getDistributors() {
  isLoading.value = true;
  const fetchDistributor = {
    Distribuidor: {
      request: () => getDistributor(page.props.auth.user.idDistribuidor),
      loadingText: 'Carregando informações...',
      successText: 'Informações carregadas com sucesso!',
      errorText: 'Erro ao carregar informações',
      onSuccess: (response) => {
        const { id, nome } = response.data.data;
        distributorName.value = StringUtil.utf8Decode(nome);

        distributors.value = [
          {
            id,
            nome: distributorName.value,
          },
        ];
        selectedDistributors.value = [distributorName.value];
        distributorName.value = StringUtil.utf8Decode(response.data.data.nome);
        isLoading.value = false;
        fetchStockReport();
      },
    },
    Administrador: {
      request: () => listAllDistributors(),
      loadingText: 'Carregando distribuidores...',
      successText: 'Distribuidores carregados com sucesso!',
      errorText: 'Erro ao carregar distribuidores',
      onSuccess: (response) => {
        distributors.value = response.data.data.map((d) => ({
          id: d.id,
          nome: StringUtil.utf8Decode(d.nome),
        }));
        isLoading.value = false;
      },
    },
  };
  const { request, loadingText, successText, errorText, onSuccess } = fetchDistributor[
    tipoAdministrador
  ]
    ? fetchDistributor[tipoAdministrador]
    : fetchDistributor.Administrador;

  renderToast(request(), loadingText, successText, errorText, onSuccess);
}

const resetValues = () => {
  stockResponse.value = null;
  searchTerm.value = '';
  selectedDistributors.value = [];
  selectedDistributorIds.value = [];
  isLoadingReport.value = false;
  filteredData.value = [];
};

const handleDialogOpen = (op) => {
  if (!op) {
    resetValues();
    props.toggleDialog();
    return;
  }
  getDistributors();
  props.toggleDialog();
};

getDistributors();
</script>

<template>
  <Dialog :open="props.isOpen" @update:open="handleDialogOpen">
    <slot name="trigger" />
    <DialogContent
      class="max-w-[90vw] sm:max-w-3xl"
      :class="{
        'overflow-auto px-2 overflow-x-hidden': stockResponse,
        'overflow-visible': !stockResponse,
      }"
    >
      <DialogHeader class="flex justify-between items-center mb-3">
        <div class="flex items-center gap-4">
          <DialogTitle class="text-info">Relatório de Estoque</DialogTitle>
        </div>
        <Button
          v-if="stockResponse && tipoAdministrador !== 'Distribuidor'"
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
          @click="resetValues()"
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

      <div v-if="stockResponse" class="flex flex-col h-[80vh]" @click.stop>
        <div class="nested-dialog-context" @click.stop>
          <DataTableStock
            :selectedDistributorsIds="selectedDistributorIds"
            :stockResponse="stockResponse"
            ajustClass="!top-[100px] md:!top-[90px]"
            :isNestedTable="true"
            @update:filteredData="(data) => (filteredData = data)"
          />
        </div>
      </div>

      <Form v-else @submit="fetchStockReport">
        <div class="flex items-center justify-center">
          <Skeleton v-if="isLoading" class="w-full h-14 rounded-lg" />
          <div v-else class="w-full">
            <span v-if="tipoAdministrador == 'Distribuidor'" class="text-info">
              {{ distributorName !== null ? distributorName : administratorName }}
            </span>
            <DistributorCombobox
              v-else
              v-model="selectedDistributors"
              :search-term="searchTerm"
              :distributors="filteredDistributors"
              :disabled="isLoadingReport"
              @update:searchTerm="searchTerm = $event"
            />
          </div>
        </div>
        <Button type="submit" class="mt-4 disabled:cursor-not-allowed" :disabled="isLoadingReport"
          >Gerar Relatório</Button
        >
      </Form>
    </DialogContent>
  </Dialog>
</template>
