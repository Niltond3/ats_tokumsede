<script setup>
import { useReminderActions } from '@/composables/useReminderActions';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

const props = defineProps({
  reminder: {
    type: Object,
    default: null,
  },
  clientId: {
    type: Number,
    required: true,
  },
  clientName: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(['saved', 'cancel']);
const { isSubmitting, createReminder, updateReminder } = useReminderActions();

const handleSubmit = async (values) => {
  const reminderData = {
    ...values,
    id_cliente: props.clientId,
    nome_cliente: props.clientName,
  };

  let result;
  if (props.reminder) {
    result = await updateReminder(props.reminder.id, reminderData);
  } else {
    result = await createReminder(reminderData);
  }

  emit('saved', result); // Pass the result data
};
</script>

<template>
  <Form
    :validation-schema="
      toTypedSchema(
        z.object({
          descricao: z.string().min(1, 'Descrição é obrigatória'),
          data_limite: z.string().nullable().optional(),
        }),
      )
    "
    :initial-values="{
      descricao: props.reminder?.descricao ?? '',
      data_limite: props.reminder?.data_limite
        ? new Date(props.reminder.data_limite).toISOString().split('T')[0]
        : '',
    }"
    keep-values
    class="bg-white/10 backdrop-blur-sm rounded-lg p-4 transition-all duration-300 ease-in-out"
    @submit="handleSubmit"
  >
    <div class="space-y-4">
      <FormField v-slot="{ field, errors }" name="descricao">
        <FormItem>
          <FormLabel
            class="bg-transparent text-white/90 font-display text-sm uppercase tracking-wide"
            >Descrição</FormLabel
          >
          <FormControl>
            <Textarea
              v-bind="field"
              :disabled="isSubmitting"
              placeholder="Digite a descrição do lembrete"
              class="bg-white/10 border-white/20 text-white placeholder:text-white/60 focus:bg-white/20 transition-colors duration-300"
            />
          </FormControl>
          <FormMessage class="text-destructive">{{ errors[0] }}</FormMessage>
        </FormItem>
      </FormField>

      <FormField v-slot="{ field, errors }" name="data_limite">
        <FormItem>
          <FormLabel
            class="bg-transparent text-white/90 font-display text-sm uppercase tracking-wide"
            >Prazo</FormLabel
          >
          <FormControl>
            <Input
              type="date"
              v-bind="field"
              :disabled="isSubmitting"
              :min="new Date().toISOString().split('T')[0]"
              class="bg-white/10 border-white/20 text-white focus:bg-white/20 transition-colors duration-300"
            />
          </FormControl>
          <FormMessage class="text-destructive">{{ errors[0] }}</FormMessage>
        </FormItem>
      </FormField>

      <div class="flex justify-end gap-2 pt-2">
        <Button
          type="button"
          variant="ghost"
          :disabled="isSubmitting"
          class="text-white hover:bg-white/20 transition-colors duration-300 hover:text-info"
          @click="emit('cancel')"
        >
          Cancelar
        </Button>
        <Button
          type="submit"
          :disabled="isSubmitting"
          class="bg-info hover:bg-info/80 text-white transition-colors duration-300"
        >
          {{ isSubmitting ? 'Salvando...' : reminder ? 'Atualizar' : 'Criar' }}
        </Button>
      </div>
    </div>
  </Form>
</template>
