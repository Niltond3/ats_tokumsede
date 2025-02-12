<script setup>
import { ref } from 'vue';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { RegisterDetails } from '@/components/forms/registerClient';
import { dialogState } from '@/composables/useToggleDialog';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
const props = defineProps({
  clientDetails: { type: Object, required: false },
  dialogControllers: { type: Object, required: false },
});

const { isOpen, toggleDialog } = props.dialogControllers || dialogState('EditClient');

const details = ref();

const emits = defineEmits(['update:dataTable']);

const handleDialogOutsideInteract = (event) => {
  const classes = [];
  event.composedPath().forEach((element) => {
    if (element.classList) {
      classes.push(Array.from(element.classList));
    }
  });
  if (classes.join(' -').includes('pac-container')) event.preventDefault();
};

const handleSucess = () => {
  emits('update:dataTable');
  toggleDialog();
};

const whenDialogOpen = () => {
  const { dataNascimento, email, nome, sexo, telefone, tipoPessoa, id, outrosContatos } =
    props.clientDetails;

  details.value = {
    id,
    dataNascimento: dataNascimento ? `${dataNascimento}` : '',
    email: email ? email : '',
    nome,
    sexo: sexo ? `${sexo}` : '',
    telefone: telefone ? telefone : '',
    tipoPessoa: tipoPessoa ? tipoPessoa : '',
    outrosContatos: outrosContatos ? outrosContatos : '',
  };
};
const handleDialogOpen = () => {
  isOpen && whenDialogOpen();
  return isOpen.value;
};
</script>

<template>
  <Dialog :open="handleDialogOpen()" @update:open="(op) => toggleDialog()">
    <DialogTrigger v-if="!dialogControllers" as-child>
      <DropdownMenuItem class="cursor-pointer gap-2" @select="(e) => e.preventDefault()">
        <i class="ri-pencil-fill text-info" />
        Editar Cliente
      </DropdownMenuItem>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[440px]" @interact-outside="handleDialogOutsideInteract">
      <DialogHeader>
        <DialogTitle class="text-info gap-1 flex items-center"
          ><i class="ri-user-add-fill"></i>Editar Cliente
        </DialogTitle>
        <DialogDescription class="text-xs text-start">
          Após terminar todos os passos, clique em "Salvar" para concluir.
        </DialogDescription>
      </DialogHeader>
      <div
        class="grid gap-4 py-4 px-1 max-h-96 overflow-y-scroll text-xs scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2"
      >
        <RegisterDetails :client-details="details" @create:success="handleSucess"></RegisterDetails>
      </div>
    </DialogContent>
  </Dialog>
</template>
