<script setup>
import { ref } from 'vue';
import axios from 'axios';
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
import DialogConfirmAction from '@/components/dialogs/DialogConfirmAction.vue';
import DialogEditClient from '@/components/dialogs/DialogEditClient.vue';
import renderToast from '@/components/renderPromiseToast';
import { updateClient } from '@/services/api/client';

const props = defineProps({
  payloadData: { type: null, required: true },
  dataTable: { type: null, required: true },
});

const { rowData } = props.payloadData;
const { id: idCliente } = rowData;
const dropdownOpen = ref(false);

function copy() {
  navigator.clipboard.writeText(props.payloadData);
}

const handleUpdateDataTable = () => props.dataTable.ajax.reload();
const handleToggleDropdown = (op) => {
  if (op || op == false) dropdownOpen.value = !dropdownOpen.value;
};

const handleStatusClientChange = ({ id, status }) => {
  renderToast(
    updateClient(idCliente, { status: id }),
    'alt erando status do cliente',
    `O cliente ${idCliente} foi ${status} com sucesso!`,
    'Erro ao alterar status do cliente',
    handleUpdateDataTable,
  );
};
</script>

<template>
  <DropdownMenu :open="dropdownOpen" @update:open="handleToggleDropdown">
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="transition-colors text-cyan-700 p-0">
        <span class="sr-only">Abrir Menú</span>
        <i class="ri-menu-fill"></i>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end" class="border border-slate-200">
      <DropdownMenuLabel class="text-info">Ações</DropdownMenuLabel>
      <DropdownMenuItem
        class="gap-2 text-muted-foreground cursor-pointer"
        @click="() => props.dataTable.ajax.reload()"
      >
        <i class="ri-eye-fill text-info"></i>
        Visualizar Cliente
      </DropdownMenuItem>
      <DialogEditClient :client-details="rowData" @update:data-table="handleUpdateDataTable" />
      <DropdownMenuSeparator />
      <DropdownMenuSub>
        <DropdownMenuSubTrigger class="text-info"> Outros </DropdownMenuSubTrigger>
        <DropdownMenuPortal>
          <DropdownMenuSubContent class="border border-slate-200">
            <DialogConfirmAction
              dialog-description="Deseja realmente inativar o cliente?"
              dialog-title="Inativar Cliente"
              trigger-icon="ri-pause-circle-fill"
              trigger-label="Inativar Cliente"
              variant="warning"
              @update:dialog-open="handleToggleDropdown"
              @on:confirm="() => handleStatusClientChange({ id: 2, status: 'inativado' })"
            />
            <DialogConfirmAction
              dialog-description="Deseja realmente Excluir o cliente?"
              dialog-title="Excluir Cliente"
              trigger-icon="ri-delete-bin-6-fill"
              trigger-label="Excluir Cliente"
              variant="danger"
              @update:dialog-open="handleToggleDropdown"
              @on:confirm="() => handleStatusClientChange({ id: 3, status: 'excluido' })"
            />
          </DropdownMenuSubContent>
        </DropdownMenuPortal>
      </DropdownMenuSub>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
