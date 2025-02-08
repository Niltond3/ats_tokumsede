<script setup>
import { ref, onMounted, watch } from 'vue';
import {
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
  SelectLabel,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { StringUtil, ClipboardUtil, OrderUtil, MoneyUtil } from '@/util';
import renderToast from '@/components/renderPromiseToast';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import { useQzTray } from '@/composables/useQzTray';
import { connectPrinter, printMobileData } from '@/services/printer/MobilePrinterService';
import usePrintOrder from '@/composables/usePrintOrders';
import useIsMobile from '@/composables/useIsMobile';
import ReminderManager from '@/components/ReminderManager.vue';
import { viewOrder } from '@/services/api/orders';

const { checkConnection, selectedPrinter, findPrinter, listPrinters, print } = useQzTray();
const { isMobile, detectDevice } = useIsMobile();
const { imprimirPedido } = usePrintOrder();

const props = defineProps({
  orderId: { type: Number, required: true },
  reminders: { type: Array, required: false, default: null },
  isOpen: { type: Boolean, required: false },
});

const isLoading = ref(true); // Estado de carregamento
const data = ref({});
const printerList = ref([]);
const clientId = ref(null);
const clientName = ref(null);

onMounted(() => {
  detectDevice();
});

const { toCurrency } = MoneyUtil.formatMoney();

const fetchOrder = () => {
  console.log(props.orderId);
  renderToast(
    viewOrder(props.orderId),
    `carregando pedido ${props.orderId}`,
    'Pedido carregado',
    'Erro ao carregar pedido',
    async (response) => {
      console.log(response.data);
      console.log(response);
      const formatedOrder = OrderUtil.formatOrder(response.data);

      const itensPedido = response.data.itensPedido.map((order) => {
        return {
          ...order,
          preco: toCurrency(order.preco),
          subtotal: toCurrency(order.subtotal),
          produto: { ...order.produto, nome: StringUtil.utf8Decode(order.produto.nome) },
        };
      });

      data.value = { ...formatedOrder, itensPedido };
      clientId.value = data.value.cliente.id;
      clientName.value = data.value.cliente.nome;
      console.log();
      isLoading.value = false;
    },
  );
};

watch(
  () => props.isOpen,
  (isOpen) => {
    console.log(isOpen);
    fetchOrder();
  },
);

const handleCopyOrder = (order) => ClipboardUtil.orderToClipboard(order);

const handlePrint = async () => {
  try {
    const printerData = imprimirPedido(data.value, selectedPrinter.value);
    await print(printerData);
  } catch (error) {
    console.error(error);
  }
};
const handlePrintOrder = () => {
  if (isMobile.value) {
    renderToast(
      connectPrinter(),
      'conectando-se a impressora',
      'Conectado',
      'Erro ao conectar-se a impressora',
      () => {
        const printerData = imprimirPedido(data.value, selectedPrinter.value);
        renderToast(
          printMobileData(printerData),
          'imprimindo pedido',
          'pedido impresso',
          'Erro ao imprimir pedido',
        );
      },
    );
    return;
  }
  const promiseConnection = checkConnection();
  renderToast(
    promiseConnection,
    'Verificando conexão',
    'Conectado',
    'Erro ao conectar-se a impressora',
    () => {
      const promiseFindPrinter = findPrinter(selectedPrinter.value);
      renderToast(
        promiseFindPrinter,
        'Procurando impressora padrão',
        'impressora encontrada',
        'Impressora padrão não encontrada',
        handlePrint,
        () => {
          const promiseListPrinters = listPrinters();
          renderToast(
            promiseListPrinters,
            'Listando impressoras',
            'Lista Obtida',
            'Erro ao listar impressoras',
            (response) => (printerList.value = response),
          );
        },
      );
    },
  );
};
</script>

<template>
  <DialogContent
    class="flex flex-col text-sm max-w-[22rem] sm:max-w-[30rem] md:max-w-[40rem] [&_div]:w-full [&_div]:flex [&_div]:flex-wrap gap-4 max-h-[560px] overflow-scroll scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2 text-slate-500"
    @pointerdownOutside.prevent
    @close.prevent
  >
    <DialogHeader>
      <DialogTitle class="font-medium text-info mr-4">
        <div v-if="isLoading" class="leading-none flex gap-3 justify-between">
          <Skeleton class="w-24 h-5" />
          <Skeleton class="w-48 h-5" />
        </div>
        <div v-else class="leading-none flex gap-3 justify-between">
          <div class="flex gap-2">
            <button class="group" @click="handleCopyOrder(data)">
              #{{ data.id }}
              <i class="ri-file-copy-fill opacity-75 group-hover:opacity-100 transition-all" />
            </button>
            <button
              class="relative min-w-[32px] min-h-[32px] w-[32px] h-[32px] text-2xl shadow-md rounded-full hover:text-info/100 text-info/60 transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg flex justify-center items-center"
              @click="handlePrintOrder"
            >
              <i class="ri-printer-fill"></i>
            </button>
            <Select
              v-if="printerList.length > 0"
              :default-value="selectedPrinter"
              @update:modelValue="
                (value) => {
                  selectedPrinter = value;
                }
              "
            >
              <SelectTrigger>
                <SelectValue placeholder="Selecione uma impressora" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Impressoras</SelectLabel>
                  <SelectItem v-for="printer in printerList" :key="printer" :value="printer">
                    {{ printer }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>
          <span class="text-sm">{{ data.distribuidor?.nome }}</span>
        </div>
      </DialogTitle>
      <DialogDescription>Detalhes do pedido</DialogDescription>
    </DialogHeader>
    <Separator label="cliente" />
    <div v-if="isLoading" class="flex justify-between">
      <span class="flex gap-1 w-1/3">
        <i class="ri-contacts-fill" />
        <Skeleton class="w-48 h-5" />
      </span>
      <span class="flex gap-1 w-1/3">
        <i class="ri-phone-fill" />
        <Skeleton class="w-48 h-5" />
      </span>
    </div>
    <div v-else class="justify-between">
      <span><i class="ri-contacts-fill" /> {{ data.cliente?.nome }}</span>
      <span><i class="ri-phone-fill" /> {{ data.cliente?.telefone }}</span>
    </div>
    <Separator label="endereço de entrega" />
    <div v-if="isLoading" class="flex-col">
      <Skeleton
        v-for="(_, index) in 3"
        :id="`address-skeleton-${index}`"
        :key="index"
        class="w-96 h-5"
      />
    </div>
    <div v-else class="flex-col">
      <span
        >{{ data.endereco?.logradouro }}, {{ `nº ${data.endereco?.numero}` || 'SN' }} -
        {{ data.endereco?.bairro }}</span
      >
      <span v-if="data.endereco?.complemento">Complemento: {{ data.endereco.complemento }}</span>
      <span v-if="data.endereco?.referencia">Referência: {{ data.endereco.referencia }}</span>
      <span class="text-xs opacity-60"
        >{{ data.endereco?.cidade }} - {{ data.endereco?.estado }}
        <span v-if="data.endereco?.cep">, {{ data.endereco?.cep }}</span></span
      >

      <span
        v-if="data.endereco?.apelido"
        class="bg-info text-white w-min py-px px-2 rounded-full font-semibold"
        >{{ data.endereco.apelido }}</span
      >
    </div>
    <Separator label="outros detalhes" />
    <div v-if="isLoading" class="flex-col relative gap-2 content-start">
      <div
        v-for="(_, index) in 3"
        :id="`detail-skeleton-${index}`"
        :key="index"
        class="flex gap-1 items-center"
      >
        <Skeleton class="w-96 h-5" />
      </div>
    </div>
    <div v-else class="flex-col relative gap-2 content-start">
      <span
        class="relative bg-info text-white w-min flex-nowrap py-1 px-2 rounded-full font-semibold pointer-events-none whitespace-nowrap"
      >
        {{ data.origem }}
        <span
          class="absolute left-1/2 -translate-x-1/2 z-10 -top-3 text-xs rounded-md bg-white text-info px-1"
          >origem</span
        >
      </span>
      <div v-if="data.agendado" class="flex gap-1 items-center">
        <span class="w-[5.9rem] flex text-xs opacity-70 justify-start"> Horário Agendamento </span>
        <i class="ri-calendar-schedule-fill" />
        {{ data.dataAgendada }}
        <i class="ri-timer-fill" />
        {{ data.horaInicio }}
      </div>

      <div v-for="detail in data.details" :key="detail.id" class="flex gap-1 items-center">
        <span class="w-[5.9rem] flex text-xs opacity-70 justify-start">
          {{ detail.label.long }}
        </span>
        <i :class="[detail.classIcon, detail.classColor]" />
        {{ detail.data }}
        <span v-if="detail.author !== ''" class="flex gap-1">
          <span class="text-xs opacity-70 justify-start w-[5.9rem] md:w-auto">responsável</span>
          <span class="text-sm text-slate-500 justify-start">{{ detail.author }}</span>
        </span>
        <span v-if="detail.reason">
          <span class="text-xs opacity-70 justify-start">motivo</span> {{ detail.reason }}</span
        >
      </div>
    </div>
    <Separator label="produtos " />
    <div v-if="isLoading">
      <div v-for="(_, index) in 3" :id="`products-skeleton-${index}`" :key="index">
        <Skeleton class="w-72 h-5" />
      </div>
    </div>
    <div v-else>
      <div v-for="order in data.itensPedido" :key="order.id">
        <p>
          {{ order.qtd }} {{ StringUtil.utf8Decode(order.produto.nome) }}
          <span class="text-xs opacity-70 justify-start">un</span>
          {{ toCurrency(order.preco) }}
          <span class="text-xs opacity-70 justify-start">subtotal</span>
          {{ toCurrency(order.subtotal) }}
        </p>
      </div>
    </div>
    <p v-if="isLoading" class="flex">
      <span class="text-xs opacity-70 justify-start">total</span>
      <Skeleton class="w-44 h-5" />
    </p>
    <p v-else class="">
      <span class="text-xs opacity-70 justify-start">total</span>
      <span
        class="relative bg-success text-white w-min flex-nowrap py-1 px-2 rounded-lg font-semibold pointer-events-none mx-2"
      >
        {{ data.total }}
        <span
          class="absolute left-1/2 -translate-x-1/2 z-10 -top-3 text-xs rounded-md bg-white text-success px-1"
          >{{ data.formaPagamento }}</span
        >
      </span>
      <span v-if="data.trocoPara != 'R$ 0,00'">
        <span class="text-xs opacity-70 justify-start"
          >troco para {{ data.trocoPara }}
          <i class="ri-arrow-left-right-line"></i>
        </span>
        {{ data.troco }}
      </span>
    </p>
    <ReminderManager
      button-class="bottom-[30.5rem] right-[7.5rem] min-[640px]:right-5 "
      :reminders="reminders"
      :client-id="clientId"
      :client-name="clientName"
    />
  </DialogContent>
</template>
