<script setup>
import { ref } from 'vue';
import DialogSelectDeliveryMan from './DialogSelectDeliveryMan.vue';
import DialogEditOrder from './DialogEditOrder.vue';
import DialogShowOrder from './DialogShowOrder.vue';
import DialogConfirmAction from './DialogConfirmAction.vue';
import { usePage } from '@inertiajs/vue3';
import { useOrderActions } from '@/composables/useOrderActions';

const page = usePage();

const props = defineProps({
  payloadData: { type: null, required: true },
  entregadores: { type: null, required: true },
  loadTable: { type: Function, required: true },
  isNestedTable: { type: Boolean, required: false },
});

const orderStatus = ref(props.payloadData.status.label);

const { tipoAdministrador } = page.props.auth.user;

const emit = defineEmits(['callback:edited-order', 'update:']);

const { id: idPedido } = props.payloadData;

const { handleAceitar, handleDespachar, handleEntregar, handleCancelar, handleToPendent } =
  useOrderActions(idPedido, props.loadTable);

const handleEditOrder = () => emit('callback:edited-order');
</script>

<template>
  <div :class="['flex gap-3', isNestedTable ? '' : 'min-[426px]:hidden']">
    <DialogShowOrder :dropdown="false" :order-id="idPedido" />
    <DialogEditOrder
      v-if="tipoAdministrador === 'Administrador'"
      :dropdown="false"
      :order-id="idPedido"
      @callback:edit-order="handleEditOrder"
    />
    <button
      v-if="orderStatus == 'Pendente'"
      class="h-8 w-8 rounded-full bg-success/80 focus:bg-success text-white shadow-sm hover:shadow-md transition-all"
      @click="handleAceitar()"
    >
      <i class="ri-check-fill"></i>
    </button>
    <DialogSelectDeliveryMan
      v-if="orderStatus == 'Aceito'"
      :dropdown="false"
      :entregadores="entregadores"
      @on:delivery-man-selected="handleDespachar"
    />
    <button
      v-if="orderStatus == 'Despachado'"
      class="h-8 w-8 rounded-full bg-accepted/80 focus:bg-accepted text-white shadow-sm hover:shadow-md transition-all"
      @click="handleEntregar()"
    >
      <i class="ri-check-double-fill"></i>
    </button>
    <DialogConfirmAction
      v-if="orderStatus !== 'Recusado'"
      :dropdown="false"
      dialog-title="Cancelar Pedido"
      trigger-icon="ri-close-circle-fill"
      trigger-label="Cancelar"
      variant="danger"
      :text-reson="true"
      @on:confirm="handleCancelar"
    />
    <DialogConfirmAction
      v-if="orderStatus !== 'Pendente'"
      :dropdown="false"
      dialog-title="Retornar para Pendente"
      trigger-icon="ri-arrow-go-back-fill"
      trigger-label="Para Pendente"
      variant="warning"
      @on:confirm="handleToPendent"
    />
  </div>
</template>
