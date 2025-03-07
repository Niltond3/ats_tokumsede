<script setup>
import { FormRegisterClient } from '@/components/forms/registerClient';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { dialogState } from '@/composables/useToggleDialog';

const emits = defineEmits(['update:dataTable']);

const { isOpen, toggleDialog } = dialogState('RegisterClient');

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
</script>

<template>
  <Dialog :open="isOpen" @update:open="(op) => toggleDialog()">
    <slot>
      <DialogTrigger as-child>
        <button
          data-long-press-delay="500"
          class="group relative inline-flex items-center justify-start overflow-hidden rounded-xl bg-info/70 px-4 py-3 font-medium transition-all"
        >
          <span
            class="absolute right-0 top-0 inline-block h-4 w-4 rounded bg-info transition-all duration-500 ease-in-out group-hover:-mr-4 group-hover:-mt-3"
          >
            <span
              class="absolute right-0 top-0 size-5 -translate-y-1/2 translate-x-1/2 rotate-45 bg-white"
            ></span>
          </span>
          <span
            class="absolute bottom-0 left-0 h-full w-full -translate-x-full translate-y-full rounded-2xl bg-info/80 transition-all delay-200 duration-500 ease-in-out group-hover:mb-12 group-hover:translate-x-0"
          ></span>
          <span
            class="relative w-full text-left text-white transition-colors duration-200 ease-in-out group-hover:text-white flex gap-3"
          >
            <i class="ri-user-add-fill" />
            Registrar cliente
          </span>
        </button>
      </DialogTrigger>
    </slot>
    <DialogContent
      class="sm:max-w-[440px] overflow-hidden"
      @interact-outside="handleDialogOutsideInteract"
    >
      <DialogHeader>
        <DialogTitle class="text-info gap-1 flex items-center"
          ><i class="ri-user-add-fill"></i>Cadastrar Cliente
        </DialogTitle>
        <DialogDescription class="text-xs text-start">
          Após terminar todos os passos, clique em "Cadastrar" para concluir.
        </DialogDescription>
      </DialogHeader>
      <div
        class="grid gap-4 py-4 px-1 max-h-96 overflow-y-scroll text-xs scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2"
      >
        <FormRegisterClient @create:success="handleSucess"></FormRegisterClient>
      </div>
    </DialogContent>
  </Dialog>
</template>
