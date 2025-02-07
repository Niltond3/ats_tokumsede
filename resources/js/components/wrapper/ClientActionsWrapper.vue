<script setup>
import { ref } from 'vue';
import axios from 'axios';
import DialogConfirmAction from '@/components/dialogs/DialogConfirmAction.vue';
import DialogEditClient from '@/components/dialogs/DialogEditClient.vue';
import renderToast from '@/components/renderPromiseToast';
import { Switch } from '@/components/ui/switch';
import Separator from '../ui/separator/Separator.vue';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { dialogState } from '@/hooks/useToggleDialog';

const props = defineProps({
  payloadData: { type: null, required: true },
  dataTable: { type: null, required: true },
});

const confirmActionsDialogController = dialogState('ConfirmAction');
const editClientDialogController = dialogState('EditClient');

const { rowData } = props.payloadData;
const { id: idCliente } = rowData;
const dropdownOpen = ref(false);
const isActive = ref(rowData.status === 1);

function copy() {
  navigator.clipboard.writeText(props.payloadData);
}

const handleUpdateDataTable = () => props.dataTable.ajax.reload();

const handleToggleDropdown = (op) => {
  if (op || op == false) dropdownOpen.value = !dropdownOpen.value;
};

const handleStatusClientChange = ({ id, status }) => {
  var url = `clientes/${idCliente}`;
  handleToggleDropdown(true);
  const promise = axios.put(url, { status: id });
  renderToast(
    promise,
    'alterando status do cliente',
    `O cliente ${idCliente} foi ${status} com sucesso!`,
    'Erro ao alterar status do cliente',
    handleUpdateDataTable,
  );
};

const handleStatusChange = (checked) => {
  const newStatus = checked ? 1 : 2;
  const statusText = checked ? 'ativado' : 'inativado';

  const promise = axios.put(`clientes/${idCliente}`, { status: newStatus });

  renderToast(
    promise,
    'alterando status do cliente',
    `O cliente ${idCliente} foi ${statusText} com sucesso!`,
    'Erro ao alterar status do cliente',
    () => {
      handleUpdateDataTable();
      isActive.value = checked;
    },
  );
};
</script>

<template>
  <div class="flex gap-3 items-center">
    <!-- <DialogShowClient :client-details="rowData" /> -->
    <!-- Add this before your existing buttons -->
    <div class="flex flex-col w-1/2">
      <Separator class="w-full" />

      <div class="flex items-center justify-around gap-3">
        <TooltipProvider :delay-duration="100">
          <Tooltip>
            <TooltipTrigger as-child>
              <div class="flex items-center">
                <Switch :checked="isActive" @update:checked="handleStatusChange" />
              </div>
            </TooltipTrigger>
            <TooltipContent
              class="border-none text-white"
              :class="isActive ? 'bg-warning' : 'bg-success'"
            >
              <span v-if="isActive" class="font-bold">
                Ativo
                <i class="ri-arrow-right-fill"></i>
                Inativo
              </span>
              <span v-else class="font-bold">
                Inativo
                <i class="ri-arrow-right-fill"></i>
                Ativo
              </span>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>

        <Separator orientation="vertical" class="h-8" />
        <TooltipProvider :delay-duration="100">
          <Tooltip>
            <TooltipTrigger as-child>
              <button @click="() => confirmActionsDialogController.toggleDialog()">
                <i
                  class="ri-delete-bin-6-fill transition-colors text-3xl text-danger/60 hover:text-danger"
                />
              </button>
            </TooltipTrigger>
            <TooltipContent class="bg-danger border-none text-white">
              <p>Exclui cliente</p>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>
      </div>
    </div>

    <Separator orientation="vertical" class="h-8" />

    <div class="flex flex-col w-1/2 gap-1">
      <Separator class="w-full" />
      <div class="flex items-center justify-around gap-3">
        <TooltipProvider :delay-duration="100">
          <Tooltip>
            <TooltipTrigger as-child>
              <button
                class="bg-info/40 py-1 px-2 size-8 rounded-full whitespace-nowrap gap-2 flex hover:bg-info transition-colors focus-visible:outline-info"
                @click="() => editClientDialogController.toggleDialog()"
              >
                <i class="ri-pencil-fill text-white" />
              </button>
            </TooltipTrigger>
            <TooltipContent class="bg-success border-none text-white">
              <p>Editar cliente</p>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>
        <Separator orientation="vertical" class="h-8" />
        <TooltipProvider :delay-duration="100">
          <Tooltip>
            <TooltipTrigger as-child>
              <button
                class="bg-info/40 py-1 px-2 size-8 rounded-full whitespace-nowrap gap-2 flex hover:bg-info transition-colors focus-visible:outline-info"
                @click="() => editClientDialogController.toggleDialog()"
              >
                <i class="ri-eye-fill text-white" />
              </button>
            </TooltipTrigger>
            <TooltipContent class="bg-success border-none text-white">
              <p>Visualizar cliente</p>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>
      </div>
    </div>

    <DialogConfirmAction
      :dropdown="false"
      dialog-description="Deseja realmente Excluir o cliente?"
      dialog-title="Excluir Cliente"
      variant="danger"
      :dialog-controllers="confirmActionsDialogController"
      @update:dialog-open="handleToggleDropdown"
      @on:confirm="() => handleStatusClientChange({ id: 3, status: 'excluido' })"
    />
    <DialogEditClient
      :client-details="rowData"
      :dialog-controllers="editClientDialogController"
      @update:data-table="handleUpdateDataTable"
    />
  </div>
</template>
