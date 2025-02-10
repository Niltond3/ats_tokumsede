<script setup>
import { ref } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import Button from '@/components/Button.vue';
import * as z from 'zod';
import validator from 'validator';
import renderToast from '@/components/renderPromiseToast';
import { updatePassword } from '@/services/api/client';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const clientId = page.props.auth.user.id;
// Password validation schema using Zod
const formSchema = z
  .object({
    current_password: z.string().optional(),
    password: z
      .string()
      .min(5, { message: 'A senha deve ter no mínimo 5 caracteres' })
      .refine(
        (value) =>
          validator.isStrongPassword(value || '', {
            minLength: 5,
            minLowercase: 1,
            minUppercase: 1,
            minNumbers: 1,
            minSymbols: 0,
            returnScore: false,
            pointsPerUnique: 1,
            pointsPerRepeat: 0.5,
            pointsForContainingLower: 10,
            pointsForContainingUpper: 10,
            pointsForContainingNumber: 10,
            pointsForContainingSymbol: 10,
          }),
        {
          message: 'Senha: 5 caracteres, com maiúscula, minúscula e número.',
        },
      ),
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'As senhas não conferem',
    path: ['password_confirmation'],
  });

const { handleSubmit, setFieldValue, values, errors, resetForm } = useForm({
  validationSchema: toTypedSchema(formSchema),
  initialValues: {
    current_password: '',
    password: null,
    password_confirmation: null,
  },
});

const onSubmit = handleSubmit((formValues) => {
  const payload = {
    old_password: formValues.current_password.length === 0 ? null : formValues.current_password,
    new_password: formValues.password,
  };

  // Here you can use your API call or Inertia form submission
  renderToast(
    updatePassword(clientId, payload),
    'Atualizando Senha ...',
    'Senha atualizada com sucesso!',
    'Erro ao atualizar senha',
    () => resetForm(),
    () => resetForm(),
  );
});

// Style constants remain the same
const inputClass = `peer w-full rounded-md border-gray-300 py-3 px-4
    placeholder-transparent
    focus:border-info focus:ring-info
    transition-all duration-300 ease-in-out`;

const labelClass = `absolute -top-2.5 left-2 text-sm !mt-0 rounded-md
    transition-all duration-300 ease-in-out
    peer-placeholder-shown:text-base
    peer-placeholder-shown:top-1/2
    peer-placeholder-shown:-translate-y-1/2
    peer-placeholder-shown:text-gray-400
    peer-focus:-top-1
    peer-focus:text-sm
    peer-focus:text-info
    bg-white px-1`;
</script>

<template>
  <div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl text-info/80 font-semibold text-center mb-6">Atualizar Senha</h2>
    <form class="space-y-6" @submit.prevent="onSubmit">
      <div class="grid grid-cols-1 gap-6">
        <FormField v-slot="{ componentField }" name="current_password">
          <FormItem class="relative">
            <FormControl>
              <Input
                v-bind="componentField"
                type="password"
                :class="inputClass"
                placeholder="Senha atual"
              />
            </FormControl>
            <FormLabel :class="labelClass">Senha Atual</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="password">
          <FormItem class="relative">
            <FormControl>
              <Input
                v-bind="componentField"
                type="password"
                :class="inputClass"
                placeholder="Nova senha"
              />
            </FormControl>
            <FormLabel :class="labelClass">Nova Senha</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="password_confirmation">
          <FormItem class="relative">
            <FormControl>
              <Input
                v-bind="componentField"
                type="password"
                :class="inputClass"
                placeholder="Confirme a nova senha"
              />
            </FormControl>
            <FormLabel :class="labelClass">Confirmar Nova Senha</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>
      </div>

      <div class="flex justify-end">
        <Button
          type="submit"
          class="inline-flex items-center px-6 py-2 bg-info text-white rounded-md hover:bg-info-dark transition-colors"
        >
          Salvar Alterações
        </Button>
      </div>
    </form>
  </div>
</template>
