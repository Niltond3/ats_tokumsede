<script setup>
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { dialogState } from '@/composables/useToggleDialog';
import { twMerge } from 'tailwind-merge';
import { FormControl, FormField, FormItem } from '@/components/ui/form';

const { isOpen, toggleDialog } = dialogState('OthersContacts');

const emit = defineEmits(['callback:orderNote']);

const props = defineProps(['orderNote', 'class']);

const note = ref(props.orderNote);

const handleClick = () => {
  emit('callback:orderNote', note.value);
  toggleDialog();
};

watch(
  () => props.orderNote,
  (newValue) => {
    note.value = newValue;
  },
);
</script>

<template>
  <Dialog :open="isOpen" @update:open="() => toggleDialog()">
    <DialogTrigger as-child>
      <button
        :class="
          twMerge(
            'absolute bg-dispatched p-0 rounded-md w-6 h-6 flex justify-center items-center shadow-lg transition-all hover:shadow-sm hover:rounded-full hover:w-8 hover:h-8 top-[-0.75rem] right-[-0.75rem] z-10 border-2 border-white',
            props.class,
          )
        "
      >
        <i class="ri-phone-fill text-white"></i>
      </button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[440px] gap-3 flex flex-col">
      <DialogHeader class="text-info">
        <div class="flex flex-row items-center gap-2">
          <i class="ri-sticky-note-fill text-3xl" />
          <DialogTitle>Outros Contatos</DialogTitle>
        </div>
        <DialogDescription>Adicione outras formas de contato</DialogDescription>
      </DialogHeader>
      <FormField v-slot="{ componentField }" name="outrosContatos">
        <FormItem>
          <FormControl>
            <Textarea
              :model-value="note"
              class="border rounded-md border-gray-200 focus-visible:ring-0 focus-visible:ring-offset-0"
              v-bind="componentField"
            ></Textarea>
          </FormControl>
        </FormItem>
      </FormField>
      <DialogFooter>
        <Button
          type="submit"
          class="border-none rounded-xl px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all"
          @click="handleClick"
        >
          Criar
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
