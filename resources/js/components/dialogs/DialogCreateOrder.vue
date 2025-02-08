<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from 'radix-vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { DataTableProducts } from '@/components/DataTableProducts';
import { StringUtil } from '@/util';
import renderToast from '@/components/renderPromiseToast';
import ReminderManager from '@/components/ReminderManager.vue';
import { useReminders } from '@/composables/useReminders';

const props = defineProps({
  open: { type: Boolean, required: false },
  toggleDialog: { type: Function, required: false },
  idClienteAddress: { type: String, required: false },
  reminders: { type: Array, required: false, default: null },
  clientName: { type: String, required: false },
  setTab: { type: Function, required: true },
});

const createOrderData = ref();

const updateTable = ref(false);

const emits = defineEmits(['update:modelValue', 'update:dataTable']);

const forwarded = useForwardPropsEmits(props, emits);

const whenDialogOpen = () => {
  console.log('open dialog');
  const url = `produtos/${props.idClienteAddress}`;
  const promise = axios.get(url);

  renderToast(
    promise,
    'carregando produtos',
    'Produtos carregados',
    'Erro ao carregar produtos',
    async (responseOrder) => {
      const { data: orderData } = responseOrder;
      const responseDistributor = orderData[1];
      const responseAddress = orderData[2];
      const address = {
        ...responseAddress,
        logradouro: StringUtil.utf8Decode(responseAddress.logradouro || ''),
        bairro: StringUtil.utf8Decode(responseAddress.bairro || ''),
        complemento: StringUtil.utf8Decode(responseAddress.complemento || ''),
        cidade: StringUtil.utf8Decode(responseAddress.cidade || ''),
        referencia: StringUtil.utf8Decode(responseAddress.referencia || ''),
        apelido: StringUtil.utf8Decode(responseAddress.apelido || ''),
        observacao: StringUtil.utf8Decode(responseAddress.observacao || ''),
      };

      const distributor = {
        ...responseDistributor,
        nome: StringUtil.utf8Decode(responseDistributor.nome),
      };
      const products = orderData[0]
        .map((product) => {
          return { ...product, nome: StringUtil.utf8Decode(product.nome) };
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
      console.log(address?.idCliente);
      const { fetchReminders, activeRemindersCount } = useReminders(address?.idCliente);
      await fetchReminders(1);
      console.log('activeRemindersCount', activeRemindersCount.value);
    },
  );
};

watch(
  () => props.open,
  () => {
    if (props.open) whenDialogOpen();
  },
);
const handleRealizarPedido = (payload) => {
  var url = 'pedidos';
  const promise = axios.post(url, payload);
  renderToast(
    promise,
    'realizando pedido',
    'Pedido realizado com sucesso!',
    'Erro ao realizar pedido',
    () => {
      props.toggleDialog();
      props.setTab('pedidos');
    },
  );
};

const handleSpecialOfferCreated = (isCreated) => (updateTable.value = isCreated);

const handleToggleDialog = () => {
  if (updateTable.value) emits('update:dataTable', true);
  updateTable.value = false;
  props.toggleDialog();
};
</script>

<template>
  <Dialog v-bind="forwarded" :open="props.open" @update:open="handleToggleDialog">
    <DialogContent class="sm:max-w-3xl">
      <DataTableProducts
        :create-order-data="createOrderData"
        @callback:payload-pedido="handleRealizarPedido"
        @update:special-offer-created="handleSpecialOfferCreated"
      />
      <ReminderManager
        :reminders="reminders"
        :client-id="createOrderData?.address?.idCliente"
        :client-name="createOrderData?.clientName"
      />
    </DialogContent>
  </Dialog>
</template>
