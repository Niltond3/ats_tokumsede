<script setup>
import { Dialog, DialogTrigger } from '@/components/ui/dialog';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { dialogState } from '@/composables/useToggleDialog';
import DialogConfirmActionContent from '@/components/DialogConfirmActionContent.vue';

const props = defineProps({
  textReson: { type: Boolean, required: false },
  triggerLabel: { type: String, required: false },
  triggerIcon: { type: String, required: true },
  dialogTitle: { type: String, required: false },
  dialogDescription: { type: String, required: false },
  variant: { type: String, required: true },
  dropdown: { type: Boolean, required: false, default: true },
});

const { isOpen, toggleDialog } = dialogState('ConfirmAction');

const emits = defineEmits(['on:confirm', 'update:dialogOpen', 'on:click']);

const handleConfirm = (confirm) => {
  emits('update:dialogOpen', false);
  emits('on:confirm', {
    reason: confirm,
    toggleDialog,
  });
};

const getVariant = {
  danger: {
    textClasses: {
      icon: 'ri-close-circle-fill',
      text: 'text-danger group-hover:text-danger',
    },
    bgClasses: 'bg-danger/70 hover:bg-danger',
  },
  warning: {
    textClasses: {
      icon: 'ri-error-warning-fill',
      text: 'text-warning group-hover:text-warning',
    },
    bgClasses: 'bg-warning/70 hover:bg-warning',
  },
};

const styleVariant = getVariant[props.variant];

const handleDialogOpen = (op) => {
  !op && emits('update:dialogOpen', op);
  toggleDialog();
};
</script>

<template>
  <Dialog :open="isOpen" @update:open="handleDialogOpen">
    <DialogTrigger as-child>
      <DropdownMenuItem
        v-if="dropdown"
        class="cursor-pointer group gap-1"
        @click="emits('on:click')"
        @select="(e) => e.preventDefault()"
      >
        <i
          :class="[props.triggerIcon, styleVariant.textClasses.text]"
          class="transition-colors"
        ></i>
        <span class="hidden min-[426px]:block">{{ props.triggerLabel }}</span>
      </DropdownMenuItem>
      <button
        v-else
        class="h-8 w-8 rounded-full text-white shadow-sm hover:shadow-md transition-all flex justify-center items-center"
      >
        <i
          :class="[props.triggerIcon, styleVariant.textClasses.text]"
          class="transition-colors text-[39px]"
        ></i>
        <span class="sr-only">{{ props.triggerLabel }}</span>
      </button>
    </DialogTrigger>
    <DialogConfirmActionContent v-bind="props" @on:confirm="handleConfirm" />
  </Dialog>
</template>
