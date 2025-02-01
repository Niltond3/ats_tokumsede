<script setup>
import { ref, onMounted, watch, reactive, nextTick, onUnmounted } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from 'radix-vue';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogFooter,
} from '@/components/ui/dialog';
import { Card, CardContent } from '@/components/ui/card';
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious,
} from '@/components/ui/carousel';
import { CommandItem } from '@/components/ui/command';
import { dateToDayMonthYearFormat, dateToISOFormat, formatMoney, utf8Decode } from '@/util';
import { toast } from 'vue-sonner';
import { cn } from '@/lib/utils';
import { dialogState } from '@/hooks/useToggleDialog';
import NumberField from './components/NumberField.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import SelectPayment from '@/components/orderComponents/SelectPayment.vue';
import ExchangeInput from '@/components/orderComponents/ExchangeInput.vue';
import DateTimePicker from '@/components/orderComponents/DateTimePicker.vue';
import DialogCreateOrderNote from '../../../../components/orderComponents/DialogCreateOrderNote.vue';
import { Check } from 'lucide-vue-next';
import renderToast from '@/components/renderPromiseToast';
import { useTableProductsState } from '@/composables/tableProductsState';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';

const tableProductsState = useTableProductsState();
const isLoading = ref(true);

const { toCurrency, toFloat } = formatMoney();

const props = defineProps({
  open: { type: Boolean, required: false },
  toggleDialog: { type: Function, required: false },
  idClienteAddress: { type: String, required: false },
  clientName: { type: String, required: false },
  //   setTab: { type: Function, required: true },
  //   address: { type: Object, required: true },
});

const createOrderData = ref();

const updateTable = ref(false);

const emits = defineEmits(['update:modelValue', 'update:dataTable', 'update:commandOpen']);

const forwarded = useForwardPropsEmits(props, emits);

// const props = defineProps({
//   setTab: { type: Function, required: false },
//   address: { type: Object, required: true },
//   value: { type: String, required: true },
// });

const numberFieldProps = ref({
  min: 0,
  modelValue: 0,
});

const disabledButton = ref(true);

const whenDialogOpen = () => {
  isLoading.value = true;

  const url = `produtos/${props.idClienteAddress}`;
  const promise = axios.get(url);

  renderToast(
    promise,
    'carregando produtos',
    'Produtos carregados',
    'Erro ao carregar produtos',
    (responseOrder) => {
      const { data: orderData } = responseOrder;

      const responseAddress = orderData[2];

      const address = {
        ...orderData[2],
        logradouro: utf8Decode(responseAddress.logradouro || ''),
        bairro: utf8Decode(responseAddress.bairro || ''),
        complemento: utf8Decode(responseAddress.complemento || ''),
        cidade: utf8Decode(responseAddress.cidade || ''),
        referencia: utf8Decode(responseAddress.referencia || ''),
        apelido: utf8Decode(responseAddress.apelido || ''),
        observacao: utf8Decode(responseAddress.observacao || ''),
      };
      const distributor = {
        ...orderData[1],
        nome: utf8Decode(orderData[1].nome),
      };

      const products = orderData[0]
        .map((product) => {
          return { ...product, nome: utf8Decode(product.nome) };
        })
        .filter((product) => product.id != 3 && product.id != 334)
        .sort();

      createOrderData.value = {
        clientName: props.clientName,
        products,
        distributor,
        address,
        distributorExpedient: orderData[6],
        distributorTaxes: orderData[4],
      };
      const {
        distributorTaxes: { taxaUnica: taxaEntrega },
        distributor: { id: idDistribuidor, observacao },
        address: { id: idEndereco },
      } = createOrderData.value;

      tableProductsState.payload = {
        ...tableProductsState.payload,
        taxaEntrega,
        idDistribuidor,
        idEndereco,
        observacao,
      };
      isLoading.value = false;
    },
  );
};

const handleDialogOpen = () => {
  props.open && whenDialogOpen();
  return props.open;
};

const handleToggleDialog = (op) => {
  if (!op && !tableProductsState.payload.isDatePickerOpen) emits('update:commandOpen', false);
  props.toggleDialog();
};

const handleDatePickerState = (state) => {
  //   isDatePickerOpen.value = state;
  //   if (state) {
  //     nextTick(() => {
  //       isOpen.value = true;
  //     });
  //   }
  //   tableProductsState.payload.isDatePickerOpen = state;
};

tableProductsState.payload.isDatePickerOpen = false;

const updateData = (rowIndex, columnId, value) => {
  const newData =
    columnId !== 'quantidade'
      ? [
          ...createOrderData.value.products.map((row, index) => {
            if (index == rowIndex) {
              const oldRow = createOrderData.value.products[rowIndex];
              return {
                ...oldRow,
                [columnId]: [
                  { qtd: oldRow[columnId][oldRow[columnId].length - 1].qtd, val: toFloat(value) },
                ],
              };
            }
            return row;
          }),
        ]
      : [
          ...createOrderData.value.products.map((row, index) => {
            if (index == rowIndex) {
              const oldRow = createOrderData.value.products[rowIndex];
              return {
                ...oldRow,
                [columnId]: value,
              };
            }
            return row;
          }),
        ];
  createOrderData.value = { ...createOrderData.value, products: newData };

  const itens = newData
    .map((product) => {
      if (product.quantidade > 0) {
        const { id, preco, quantidade } = product;
        return {
          idProduto: id,
          quantidade: quantidade,
          preco: preco[preco.length - 1].val,
          subtotal: quantidade * preco[preco.length - 1].val,
          precoAcertado: null,
        };
      }
      return null;
    })
    .filter((x) => x);

  const taxaEntrega = tableProductsState.payload.taxaEntrega;
  try {
    const totalProdutos = itens
      .map((product) => product.subtotal)
      .reduce((curr, prev) => curr + prev);
    const total = totalProdutos + taxaEntrega;

    tableProductsState.payload = {
      ...tableProductsState.payload,
      totalProdutos,
      total,
      itens,
      origem: 4,
    };
  } catch (error) {
    disabledButton.value = true;
    toast.error('Adicione ao menos um produto');
  }
};

const handlePayForm = (value) =>
  (tableProductsState.payload = { ...tableProductsState.payload, formaPagamento: value });

const handleExchange = ({ value }) =>
  (tableProductsState.payload = {
    ...tableProductsState.payload,
    trocoPara: parseFloat(value.split(' ')[1]),
  });

const handleScheduling = (date) => {
  if (date) {
    const { date: formattedDate, time } = dateToDayMonthYearFormat(date);

    const dataAgendada = formattedDate;

    const horaInicio = time;

    return (tableProductsState.payload = {
      ...tableProductsState.payload,
      agendado: 1,
      dataAgendada,
      horaInicio,
    });
  }
  return (tableProductsState.payload = {
    ...tableProductsState.payload,
    agendado: 0,
    dataAgendada: '',
    horaInicio: '',
  });
};

const handleOrderNote = (value) =>
  (tableProductsState.payload = { ...tableProductsState.payload, obs: value });

watch(
  () => tableProductsState.payload.itens,
  (newVal) =>
    (disabledButton.value =
      newVal.map((product) => product.quantidade).reduce((curr, prev) => curr + prev) < 1
        ? true
        : false),
);

const handleCallbackPedido = () => {
  disabledButton.value = true;
  renderToast(
    axios.post('pedidos', tableProductsState.payload),
    'Cadastrando pedido',
    'o pedido foi cadastrado com sucesso',
    'Ocorreu um erro ao cadastrar o pedido',
    () => {
      emits('update:dataTable', true);
      props.toggleDialog();
    },
  );
};

const useDialogObserver = () => {
  let observer;

  onMounted(() => {
    nextTick(() => {
      observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.attributeName === 'data-state') {
            const dialog = document.querySelector('[role="dialog"]');
            if (dialog) {
              const rect = dialog.getBoundingClientRect();
              dialog.style.setProperty('--dialog-top', `${rect.top}px`);
              dialog.style.setProperty('--dialog-left', `${rect.left}px`);
              dialog.style.setProperty('--dialog-width', `${rect.width}px`);
            }
          }
        });
      });

      const dialog = document.querySelector('[role="dialog"]');
      dialog && observer.observe(dialog, { attributes: true });
    });
  });

  onUnmounted(() => observer?.disconnect());
};

useDialogObserver();

//@select.prevent
</script>

<template>
  <div>
    <Dialog
      v-bind="forwarded"
      :open="handleDialogOpen()"
      :prevent-close="true"
      @update:open="handleToggleDialog"
    >
      <DialogContent
        :class="[
          'bg-info/60 backdrop-blur-sm sm:max-w-[600px] grid-rows-[auto_minmax(0,1fr)_auto] p-0 h-full max-h-full',
          'dialog-content-custom',
        ]"
        style="overflow: visible; pointer-events: all"
      >
        <DialogHeader class="p-6 pb-0 text-white">
          <DialogTitle class="">Nossos Produtos</DialogTitle>
          <DialogDescription
            class="text-white/50 text-sm flex flex-col gap-3 justify-between items-center"
          >
            Informações do pedido
            <div class="flex justify-between items-center w-full">
              <div class="flex flex-col">
                subtotal
                <p>{{ toCurrency(tableProductsState.payload.totalProdutos) }}</p>
              </div>
              <Separator orientation="vertical" class="h-[32px] bg-white/20"></Separator>
              <div class="flex flex-col">
                taxa de entrega
                <p>{{ toCurrency(tableProductsState.payload.taxaEntrega) }}</p>
              </div>
              <Separator orientation="vertical" class="h-[32px] bg-white/20"></Separator>
              <div class="flex flex-col">
                total
                <p>{{ toCurrency(tableProductsState.payload.total) }}</p>
              </div>
            </div>
          </DialogDescription>
        </DialogHeader>
        <Carousel v-slot="{ canScrollNext }" class="relative w-full max-w-full">
          <DialogCreateOrderNote
            :order-note="tableProductsState.payload.obs"
            class="top-[6%] right-[6%]"
            @callback:order-note="handleOrderNote"
          >
          </DialogCreateOrderNote>
          <CarouselContent class="bg-transparent relative !ml-0">
            <template v-if="isLoading">
              <CarouselItem v-for="n in 2" :key="n" class="md:basis-1/2">
                <div class="p-1">
                  <Card class="border-none bg-transparent">
                    <CardContent
                      class="flex aspect-square items-center justify-center p-6 gap-3 flex-col"
                    >
                      <Skeleton class="h-4 w-[200px]" />
                      <Skeleton class="h-[227px] w-[200px]" />
                      <Skeleton class="h-10 w-[150px]" />
                    </CardContent>
                  </Card>
                </div>
              </CarouselItem>
            </template>
            <template v-else>
              <CarouselItem
                v-for="(product, index) in createOrderData.products"
                :key="product.id"
                class="md:basis-1/2"
              >
                <div class="p-1">
                  <Card class="border-none bg-transparent">
                    <CardContent
                      class="flex aspect-square items-center justify-center p-6 gap-3 flex-col"
                    >
                      <div class="flex flex-col items-center justify-center">
                        <h3 class="text-white/50 text-sm">{{ product.nome }}</h3>
                        <h2 class="text-white text-base mb-4">
                          {{ toCurrency(parseFloat(product.preco[product.preco.length - 1].val)) }}
                        </h2>
                      </div>
                      <img :src="`/images/uploads/${product.img}`" class="h-[227px]" />
                      <div>
                        <NumberField
                          v-bind="numberFieldProps"
                          @update:model-value="(val) => updateData(index, 'quantidade', val)"
                        >
                        </NumberField>
                      </div>
                    </CardContent>
                  </Card>
                </div>
              </CarouselItem>
            </template>
          </CarouselContent>
          <CarouselPrevious class="left-[1rem] bg-transparent text-white !border-input/30 ring-0" />
          <CarouselNext
            v-if="canScrollNext"
            class="right-[1rem] bg-transparent text-white !border-input/30 ring-0"
          />
        </Carousel>
        <div class="flex flex-wrap gap-2 p-2 sm:h-14 justify-center mb-3">
          <SelectPayment
            :default="tableProductsState.payload.formaPagamento"
            @update:payment-form="handlePayForm"
          />
          <Separator orientation="vertical" class="" />
          <ExchangeInput
            :value="tableProductsState.payload.trocoPara"
            class="[&_input]:bg-white [&_label]:bg-transparent [&_label]:top-[-1rem] [&_label]:left-0 [&_label]:!text-input"
            @update:exchange="handleExchange"
          />
          <Separator orientation="vertical" class="hidden sm:block" />
          <DateTimePicker
            :default:scheduling="
              dateToISOFormat(
                `${tableProductsState.payload.dataAgendada} ${tableProductsState.payload.horaInicio}`,
              )
            "
            @date-picker-open="handleDatePickerState"
            @update:scheduling="handleScheduling"
          />
        </div>
        <DialogFooter>
          <Button
            :disabled="disabledButton"
            type="submit"
            class="border-none w-full rounded-none px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/20 disabled:hover:bg-info/60 disabled:cursor-not-allowed"
            @click="handleCallbackPedido"
          >
            <span> Realizar Pedido </span>
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
<style>
.dialog-content-custom {
  overflow: visible !important;
  contain: none !important;
}

:deep(.dp__outer_menu_wrap) {
  position: fixed !important;
  z-index: 99999 !important;
}
</style>
