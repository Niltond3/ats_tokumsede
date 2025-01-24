<script setup>
import { onMounted, ref } from 'vue';
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
import { useForm } from 'vee-validate';
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

onMounted(() => {
  console.log(props.reminder);
});
const emit = defineEmits(['saved', 'cancel']);

const { isSubmitting, createReminder, updateReminder } = useReminderActions();

const formSchema = toTypedSchema(
  z.object({
    descricao: z.string().min(1, 'Descrição é obrigatória'),
    data_limite: z
      .string()
      .optional()
      .transform((val) => val || null),
  }),
);

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    descricao: props.reminder?.descricao || '',
    data_limite: props.reminder?.data_limite ? props.reminder.data_limite.split('T')[0] : '',
  },
});

const handleSubmit = async (values) => {
  try {
    const reminderData = {
      ...values,
      id_cliente: props.clientId,
      nome_cliente: props.clientName,
    };

    if (props.reminder) {
      await updateReminder(props.reminder.id, reminderData);
    } else {
      await createReminder(reminderData);
    }
    emit('saved');
  } catch (error) {
    console.error('Error submitting form:', error);
  }
};
</script>

<template>
  <Form :initial-values="reminder" keep-values @submit="handleSubmit">
    <div class="space-y-4">
      <FormField v-slot="{ field, errors }" name="descricao">
        <FormItem>
          <FormLabel>Descrição</FormLabel>
          <FormControl>
            <Textarea
              v-bind="field"
              :disabled="isSubmitting"
              placeholder="Digite a descrição do lembrete"
            />
          </FormControl>
          <FormMessage>{{ errors[0] }}</FormMessage>
        </FormItem>
      </FormField>

      <FormField v-slot="{ field, errors }" name="data_limite">
        <FormItem>
          <FormLabel>Prazo</FormLabel>
          <FormControl>
            <Input
              type="date"
              v-bind="field"
              :disabled="isSubmitting"
              :min="new Date().toISOString().split('T')[0]"
            />
          </FormControl>
          <FormMessage>{{ errors[0] }}</FormMessage>
        </FormItem>
      </FormField>

      <div class="flex justify-end gap-2">
        <Button type="button" variant="ghost" :disabled="isSubmitting" @click="emit('cancel')">
          Cancelar
        </Button>
        <Button type="submit" :disabled="isSubmitting">
          {{ isSubmitting ? 'Salvando...' : reminder ? 'Atualizar' : 'Criar' }}
        </Button>
      </div>
    </div>
  </Form>
</template>
