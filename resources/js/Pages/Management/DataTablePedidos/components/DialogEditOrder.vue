<script setup>
import { ref, watch, useSlots } from 'vue';
import {
  Dialog,
  DialogContent,
  DialogTrigger,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog';
import { utf8Decode, formatMoney } from '@/util';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { formatOrder, orderToClipboard } from '../../utils';
import { DataTableProducts } from '@/components/DataTableProducts';
import { dialogState } from '@/hooks/useToggleDialog';
import renderToast from '@/components/renderPromiseToast';
import { Skeleton } from '@/components/ui/skeleton';

const props = defineProps({
  orderId: { type: Number, required: true },
  dropdown: { type: Boolean, required: false, default: true },
  // New props for external control when using slot
  modelValue: { type: Boolean, required: false },
  controlled: { type: Boolean, required: false, default: false },
});

const emits = defineEmits(['callback:editOrder', 'update:dialogOpen', 'update:modelValue']);

// Check if slot exists
const slots = useSlots();
const hasCustomTrigger = !!slots.trigger;

// Only use internal state if not controlled externally
const { isOpen, toggleDialog } = !props.controlled
  ? dialogState('EditOrder')
  : {
      isOpen: ref(props.modelValue),
      toggleDialog: () => {},
    };

const isLoading = ref(true); // Estado de carregamento
const data = ref({});
const distributors = ref([]);
const createOrderData = ref();
const distributorExpedient = ref();
const distributorTaxes = ref();
const products = ref();

const { toCurrency } = formatMoney();

const fetchOrder = () => {
  const urlOrder = `pedidos/editar/${props.orderId}`;
  const promise = axios.get(urlOrder);

  isLoading.value = true; // Atualiza o estado de carregamento para true

  renderToast(
    promise,
    `carregando pedido #${props.orderId}`,
    'sucesso ao carregar pedido',
    'erro ao carregar pedido',
    (responseOrder) => {
      const { data: orderEditData } = responseOrder;
      const orderData = orderEditData[0];
      const distributorsData = orderEditData[1];
      const clientName = utf8Decode(orderEditData[0].cliente.nome);

      distributors.value = distributorsData.filter((distributor) => distributor.status == 1);

      const idDistribuidor = orderData.distribuidor.id;
      const idCliente = orderData.cliente.id;
      const urlProducts = `produtos/listarProdutos/${idDistribuidor}/${idCliente}`;

      const productsPromise = axios.get(urlProducts);

      renderToast(
        productsPromise,
        'carregando produtos',
        'produtos carregados com sucesso',
        'erro ao carregar produtos',
        (responseProducts) => {
          const { data: productsData } = responseProducts;
          distributorExpedient.value = productsData[2];
          distributorTaxes.value = productsData[3];

          const itensPedido = orderData.itensPedido.map((order) => {
            return {
              ...order,
              preco: toCurrency(order.preco),
              subtotal: toCurrency(order.subtotal),
              produto: { ...order.produto, nome: utf8Decode(order.produto.nome) },
            };
          });
          products.value = productsData[0].map((product) => {
            const orderItem = itensPedido.find((item) => item.produto.id == product.id);
            const quantidade = orderItem ? orderItem.qtd : 0;

            return {
              ...product,
              quantidade,
            };
          });

          const formatedOrder = formatOrder(orderData);

          const order = {
            ...formatedOrder,
            itensPedido,
          };

          createOrderData.value = {
            clientName,
            order,
            products: products.value,
            distributor: order.distribuidor,
            address: order.endereco,
            distributorExpedient: distributorExpedient.value,
            distributorTaxes: distributorTaxes.value,
            formaPagamento: orderData.formaPagamento,
          };
          data.value = order;

          isLoading.value = false;
        },
      );
    },
  );
};

watch(
  () => isOpen.value,
  () => fetchOrder(),
);

const handleCopyOrder = (order) => orderToClipboard(order);

const handleDialogOpen = (op) => {
  if (props.controlled) {
    emits('update:modelValue', op);
    return;
  }
  !op && emits('update:dialogOpen', false);
  toggleDialog();
  event?.stopPropagation();
};

// Watch for external modelValue changes when controlled
watch(
  () => props.modelValue,
  (newVal) => {
    if (props.controlled) {
      isOpen.value = newVal;
    }
  },
);

// Original watch for fetching data
watch(
  () => isOpen.value,
  () => fetchOrder(),
);

const handleUpdateOrder = (payload) => {
  const url = `pedidos/atualizar/${payload.idPedido}`;
  const response = axios.put(url, payload);
  renderToast(
    response,
    'atualizando pedido',
    'pedido atualizado',
    'erro ao atualizar pedido',
    () => {
      handleDialogOpen(false);
      emits('callback:editOrder');
    },
  );
};
</script>

<template>
  <Dialog :open="controlled ? modelValue : isOpen" :modal="true" @update:open="handleDialogOpen">
    <template v-if="hasCustomTrigger">
      <slot name="trigger" />
    </template>
    <DialogTrigger v-else as-child @click.stop>
      <DropdownMenuItem
        v-if="dropdown"
        class="cursor-pointer flex gap-2"
        @select="(e) => e.preventDefault()"
      >
        <i class="ri-edit-2-fill text-info"></i>
        <span class="hidden min-[426px]:block">Editar Pedido</span>
      </DropdownMenuItem>
      <button
        v-else
        class="h-8 w-8 rounded-full bg-warning/80 focus:bg-warning text-white shadow-sm hover:shadow-md transition-all"
      >
        <i class="ri-edit-2-fill"></i>
        <span class="sr-only">Editar Pedido</span>
      </button>
    </DialogTrigger>
    <DialogContent
      class="text-sm max-w-[22rem] sm:max-w-3xl"
      @pointerDownOutside.prevent
      @close.prevent
    >
      <DialogHeader>
        <DialogTitle class="font-medium text-info leading-none">
          <div v-if="isLoading" class="flex gap-3 justify-between mr-4">
            <div class="flex gap-1">
              #
              <Skeleton class="h-5 w-20" />
            </div>
            <Skeleton class="h-5 w-40" />
          </div>
          <div v-else class="flex gap-3 justify-between mr-4">
            <button class="group" @click="handleCopyOrder(data)">
              #{{ createOrderData.order.id }}
              <i class="ri-file-copy-fill opacity-75 group-hover:opacity-100 transition-all" />
            </button>
          </div>
        </DialogTitle>
        <DialogDescription> Clique no botão "Salvar" para salvar as alterações </DialogDescription>
      </DialogHeader>

      <DataTableProducts
        :create-order-data="createOrderData"
        :distributors="distributors"
        @callback:payload-pedido="handleUpdateOrder"
      ></DataTableProducts>
    </DialogContent>
  </Dialog>
</template>
