<template>
  <div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl text-danger/80 font-semibold text-center mb-6">Excluir Conta</h2>

    <div class="space-y-6">
      <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
        Após excluir sua conta, todos os recursos e dados serão permanentemente apagados.
      </p>

      <div class="flex justify-center">
        <DangerButton class="px-6 py-2" @click="confirmUserDeletion"> Excluir Conta </DangerButton>
      </div>

      <Modal :show="confirmingUserDeletion" @close="closeModal">
        <div class="p-6">
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Tem certeza que deseja excluir sua conta?
          </h2>

          <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            Por favor, digite sua senha para confirmar que deseja excluir permanentemente sua conta.
          </p>

          <FormField v-slot="{ componentField }" name="password" class="mt-6">
            <FormItem class="relative">
              <FormControl>
                <Input
                  v-bind="componentField"
                  ref="passwordInput"
                  v-model="form.password"
                  type="password"
                  :class="inputClass"
                  placeholder="Sua senha"
                  @keyup.enter="deleteUser"
                />
              </FormControl>
              <FormLabel :class="labelClass">Senha</FormLabel>
              <FormMessage class="absolute -bottom-5 right-3" />
            </FormItem>
          </FormField>

          <div class="mt-6 flex justify-end gap-3">
            <Button class="bg-gray-200 hover:bg-gray-300" @click="closeModal"> Cancelar </Button>
            <DangerButton
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              @click="deleteUser"
            >
              Excluir Conta
            </DangerButton>
          </div>
        </div>
      </Modal>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import Button from '@/components/Button.vue';
import DangerButton from '@/components/DangerButton.vue';
import Modal from '@/components/Modal.vue';
import { deleteProfile } from '@/services/api/client';
import renderToast from '@/components/renderPromiseToast';
import { router } from '@inertiajs/vue3';
// Style constants
const inputClass = `
    peer w-full rounded-md border-gray-300 py-3 px-4
    placeholder-transparent
    focus:border-info focus:ring-info
    transition-all duration-300 ease-in-out
  `;

const labelClass = `
    absolute -top-2.5 left-2 text-sm !mt-0 rounded-md
    transition-all duration-300 ease-in-out
    peer-placeholder-shown:text-base
    peer-placeholder-shown:top-1/2
    peer-placeholder-shown:-translate-y-1/2
    peer-placeholder-shown:text-gray-400
    peer-focus:-top-1
    peer-focus:text-sm
    peer-focus:text-info
    bg-white px-1
  `;

// Component state
const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

// Initialize form
const form = useForm({
  password: '',
});

// Methods
const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  nextTick(() => passwordInput.value.focus());
};

/**
 * Handle user deletion process
 * Shows loading state, makes API call, and handles response
 */
const deleteUser = async () => {
  const result = await renderToast(
    deleteProfile({ password: form.password }),
    'Excluindo conta...',
    'Conta excluída com sucesso!',
    'Erro ao excluir conta',
    (response) => {
      // Success callback
      closeModal();
      // Redirect to home page after successful deletion
      router.visit('/');
    },
    (error) => {
      // Error callback
      passwordInput.value.focus();
      form.reset();
    },
  );
};

/**
 * Close the confirmation modal and reset form state
 */
const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.reset();
  form.clearErrors();
};
</script>
