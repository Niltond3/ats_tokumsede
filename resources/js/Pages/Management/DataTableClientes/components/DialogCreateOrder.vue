<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from 'radix-vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { DataTableProducts } from '@/components/DataTableProducts';
import { utf8Decode } from '@/util';
import renderToast from '@/components/renderPromiseToast';
import ReminderManager from '@/components/ReminderManager.vue';

const props = defineProps({
  open: { type: Boolean, required: false },
  toggleDialog: { type: Function, required: false },
  idClienteAddress: { type: String, required: false },
  clientName: { type: String, required: false },
  setTab: { type: Function, required: true },
});

const createOrderData = ref();

const updateTable = ref(false);

const emits = defineEmits(['update:modelValue', 'update:dataTable']);

const forwarded = useForwardPropsEmits(props, emits);

const whenDialogOpen = () => {
  const url = `produtos/${props.idClienteAddress}`;
  const promise = axios.get(url);

  renderToast(
    promise,
    'carregando produtos',
    'Produtos carregados',
    'Erro ao carregar produtos',
    (responseOrder) => {
      const { data: orderData } = responseOrder;
      const responseDistributor = orderData[1];
      const responseAddress = orderData[2];
      const address = {
        ...responseAddress,
        logradouro: utf8Decode(responseAddress.logradouro || ''),
        bairro: utf8Decode(responseAddress.bairro || ''),
        complemento: utf8Decode(responseAddress.complemento || ''),
        cidade: utf8Decode(responseAddress.cidade || ''),
        referencia: utf8Decode(responseAddress.referencia || ''),
        apelido: utf8Decode(responseAddress.apelido || ''),
        observacao: utf8Decode(responseAddress.observacao || ''),
      };

      const distributor = {
        ...responseDistributor,
        nome: utf8Decode(responseDistributor.nome),
      };
      const products = orderData[0];
      createOrderData.value = {
        clientName: props.clientName,
        products,
        distributor,
        address,
        distributorExpedient: orderData[6],
        distributorTaxes: orderData[4],
      };
    },
  );
};

const handleDialogOpen = () => {
  props.open && whenDialogOpen();
  return props.open;
};

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
  <Dialog v-bind="forwarded" :open="handleDialogOpen()" @update:open="handleToggleDialog">
    <DialogContent class="sm:max-w-3xl">
      <DataTableProducts
        :create-order-data="createOrderData"
        @callback:payload-pedido="handleRealizarPedido"
        @update:special-offer-created="handleSpecialOfferCreated"
      />
      <ReminderManager
        :client-id="createOrderData?.address?.idCliente"
        :client-name="createOrderData?.clientName"
      />
    </DialogContent>
  </Dialog>
</template>
