<script setup>
import { ref } from 'vue';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuPortal,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { MoreVertical } from 'lucide-vue-next';
import DialogSelectDeliveryMan from './DialogSelectDeliveryMan.vue';
import DialogEditOrder from './DialogEditOrder.vue';
import DialogShowOrder from './DialogShowOrder.vue';
import DialogConfirmAction from './DialogConfirmAction.vue';
import { usePage } from '@inertiajs/vue3';
import { useOrderActions } from '../composers/useOrderActions';

const page = usePage();

const props = defineProps({
  payloadData: { type: null, required: true },
  entregadores: { type: null, required: true },
  isNestedTable: { type: Boolean, required: false },
  loadTable: { type: Function, required: true },
});

const orderStatus = ref(props.payloadData.status.label);
const dropdownOpen = ref(false);

const { tipoAdministrador } = page.props.auth.user;

const emit = defineEmits(['callback:edited-order']);

const { id: idPedido } = props.payloadData;

const handleEditOrder = () => emit('callback:edited-order');

const { handleAceitar, handleDespachar, handleEntregar, handleCancelar, handleToPendent } =
  useOrderActions(idPedido, props.loadTable);

const handleToggleDropdown = (op) => {
  if (op || op == false) dropdownOpen.value = !dropdownOpen.value;
};
</script>

<template>
  <DropdownMenu :open="dropdownOpen" @update:open="handleToggleDropdown">
    <div>
      <DropdownMenuTrigger as-child>
        <Button
          variant="ghost"
          :class="[
            'transition-colors text-cyan-700 p-0 hidden',
            isNestedTable ? '' : 'min-[426px]:block',
          ]"
        >
          <span class="sr-only">Abrir Menú</span>
          <MoreVertical class="w-6 h-6" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" class="border border-slate-200">
        <DropdownMenuLabel class="text-info">Ações</DropdownMenuLabel>
        <DialogShowOrder :order-id="idPedido" @update:dialog-open="handleToggleDropdown" />
        <DialogEditOrder
          v-if="tipoAdministrador === 'Administrador'"
          :order-id="idPedido"
          @callback:edit-order="handleEditOrder"
          @update:dialog-open="handleToggleDropdown"
        />
        <DropdownMenuSeparator />
        <DropdownMenuSub>
          <DropdownMenuSubTrigger class="text-info"> Status </DropdownMenuSubTrigger>
          <DropdownMenuPortal>
            <DropdownMenuSubContent class="border border-slate-200">
              <DropdownMenuItem
                v-if="orderStatus == 'Pendente'"
                class="cursor-pointer flex gap-1"
                @click="handleAceitar()"
              >
                <i class="ri-check-fill"></i>
                Aceitar
              </DropdownMenuItem>
              <DialogSelectDeliveryMan
                v-if="orderStatus == 'Aceito'"
                :entregadores="entregadores"
                @on:delivery-man-selected="
                  (idDeliveryMan) => {
                    handleToggleDropdown(false);
                    handleDespachar(idDeliveryMan);
                  }
                "
              />
              <DropdownMenuItem
                v-if="orderStatus == 'Despachado'"
                class="cursor-pointer flex gap-1"
                @click="handleEntregar()"
              >
                <i class="ri-check-double-fill text-info"></i>
                Entregar
              </DropdownMenuItem>
              <DialogConfirmAction
                dialog-title="Cancelar Pedido"
                trigger-icon="ri-close-circle-fill"
                trigger-label="Cancelar"
                variant="danger"
                :text-reson="true"
                @update:dialog-open="handleToggleDropdown"
                @on:confirm="
                  (confirmCancellCalback) => {
                    const { reason, toggleDialog } = confirmCancellCalback;
                    handleCancelar(reason, toggleDialog);
                  }
                "
              />
              <DialogConfirmAction
                v-if="orderStatus !== 'Pendente'"
                dialog-title="Retornar para Pendente"
                trigger-icon="ri-arrow-go-back-fill"
                trigger-label="Para Pendente"
                variant="warning"
                @update:dialog-open="handleToggleDropdown"
                @on:confirm="handleToPendent"
              />
            </DropdownMenuSubContent>
          </DropdownMenuPortal>
        </DropdownMenuSub>
      </DropdownMenuContent>
    </div>
  </DropdownMenu>
</template>
