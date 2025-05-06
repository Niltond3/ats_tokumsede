<script setup>
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import SelectPayment from '@/components/orderComponents/SelectPayment.vue';
import ExchangeInput from '@/components/orderComponents/ExchangeInput.vue';
import DateTimePicker from '@/components/orderComponents/DateTimePicker.vue';
import { MoneyUtil, DateUtil, ClipboardUtil } from '@/util';
import { computed, ref, watch } from 'vue';
import { event } from 'jquery';

const props = defineProps({
  pix_key: String,
  payload: Object,
  isUpdate: Boolean,
  disabledButton: Boolean,
  addressNote: String,
  products: Object,
});

const paymentForm = ref(1);
const disabledPixCodeButton = ref(props.disabledButton && paymentForm.value != 3);

watch(
  () => props.disabledButton,
  (value) => {
    console.log(disabledPixCodeButton);
    disabledPixCodeButton.value = props.disabledButton && paymentForm.value != 3;
  },
);

watch(
  () => paymentForm.value,
  (value) => {
    console.log(`disabledButton: ${props.disabledButton}`);
    console.log(`paymentForm: ${value} ${value == 3}`);
    disabledPixCodeButton.value = props.disabledButton && value == 3;
  },
);

const { toCurrency } = MoneyUtil.formatMoney();

const emit = defineEmits([
  'update:paymentForm',
  'update:exchange',
  'update:scheduling',
  'update:addressNote',
  'callback:pedido',
]);

const values = computed(() => [
  {
    label: 'Produtos',
    value: toCurrency(props.payload.totalProdutos),
  },
  {
    label: 'Entrega',
    value: toCurrency(props.payload.taxaEntrega),
  },
  {
    label: 'Total',
    value: toCurrency(props.payload.total),
  },
]);

const orderProductsToClipboard = () => {
  ClipboardUtil.orderProductsToClipboard(props.payload, props.products, props.pix_key);
};
const copyPixCodeToClipboard = () => {
  ClipboardUtil.copyPixCodeToClipboard(props.payload, props.pix_key);
};
const qrCodeToClipboard = () => {
  ClipboardUtil.qrCodeToClipboard(props.payload, props.pix_key);
};
</script>

<template>
  <div class="flex mt-3 gap-7 sm:gap-4 flex-col sm:grid sm:grid-cols-12 sm:grid-rows-2">
    <div class="flex items-center h-11 justify-around sm:col-span-4 mt-3">
      <button
        class="w-full flex flex-col items-center justify-center bg-info py-3 px-4 rounded-md transition-colors duration-300 group relative group"
      >
        <p
          class="z-10 absolute opacity-0 group-hover:opacity-100 size-8 text-white bg-dispatched border-2 border-white rounded-full bottom-1/2 -translate-y-1/2 -left-3 transition-opacity duration-300"
        >
          <i class="ri-file-copy-2-fill"></i>
        </p>
        <Separator class="mt-1 mb-[0.35rem]" />
        <div class="flex gap-5">
          <span
            v-for="value in values"
            :key="value.label"
            class="text-sm font-medium relative text-info"
          >
            <p
              class="absolute -top-5 text-white text-xs -translate-x-1/2 left-1/2 bg-info p-1 rounded-md"
            >
              {{ value.label }}
            </p>
            <p class="text-nowrap text-white font-bold">{{ value.value }}</p>
          </span>
        </div>
        <div class="flex gap-2">
          <button
            class="text-lg size-8 text-white hover:bg-white hover:text-info transition-all px-1 py-0.5 rounded-full"
            @click="orderProductsToClipboard"
          >
            <i class="ri-info-card-fill"></i>
          </button>
          <button
            :disabled="disabledPixCodeButton"
            class="text-lg size-8 text-white hover:bg-white hover:text-info transition-all px-1 py-0.5 rounded-full disabled:opacity-50 disabled:select-none disabled:pointer-events-none"
            @click="copyPixCodeToClipboard"
          >
            <i class="ri-pix-fill"></i>
          </button>
          <button
            :disabled="paymentForm != 3"
            class="text-lg size-8 text-white hover:bg-white hover:text-info transition-all px-1 py-0.5 rounded-full disabled:opacity-50 disabled:select-none disabled:pointer-events-none"
            @click="qrCodeToClipboard"
          >
            <i class="ri-qr-code-fill"></i>
          </button>
        </div>
      </button>
    </div>
    <div
      class="flex flex-wrap gap-2 px-2 pb-2 sm:h-14 justify-center sm:row-start-2 sm:col-start-1 sm:col-span-10"
    >
      <Separator label="Detalhes" class="z-100 my-1" />
      <SelectPayment
        :default="payload.formaPagamento.toString()"
        @update:payment-form="
          (event) => {
            paymentForm = event;
            emit('update:paymentForm', event);
          }
        "
      />
      <Separator orientation="vertical" class="" />
      <ExchangeInput
        :value="payload.trocoPara"
        @update:exchange="$emit('update:exchange', $event)"
      />
      <Separator orientation="vertical" class="hidden sm:block" />
      <DateTimePicker
        :default:scheduling="
          DateUtil.dateToISOFormat(`${payload.dataAgendada} ${payload.horaInicio}`)
        "
        @update:scheduling="$emit('update:scheduling', $event)"
      />
    </div>
    <div class="w-full relative flex gap-1 p-2 sm:col-start-5 sm:col-end-11">
      <Textarea
        class="border rounded-md border-gray-200 min-h-11 h-11 sm:min-h-16 focus-visible:ring-0 focus-visible:ring-offset-0"
        :model-value="addressNote"
        @update:model-value="$emit('update:addressNote', $event)"
      />
      <span class="absolute text-xs text-muted-foreground left-2 -top-0 bg-white"
        >observação do endereço</span
      >
    </div>
    <Button
      :disabled="disabledButton"
      type="submit"
      class="sm:col-span-2 sm:col-end-13 sm:row-span-2 sm:my-3 border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/60 disabled:hover:bg-info/60 disabled:cursor-not-allowed"
      @click="$emit('callback:pedido')"
    >
      <span v-if="isUpdate">Salvar</span>
      <span v-else>Cadastrar</span>
    </Button>
  </div>
</template>
