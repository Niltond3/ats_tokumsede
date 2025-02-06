<script setup>
import AuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import ClientProfileForm from '@/components/forms/ClientProfileForm.vue';
import renderToast from '@/components/renderPromiseToast';
import { getClient } from '@/services/api/client';

// Adicionar props para receber os dados do cliente
defineProps({
  mustVerifyEmail: Boolean,
  status: String,
  client: {
    // Novo prop para dados do cliente
    type: Object,
    required: true,
  },
});

const page = usePage();
const clientId = page.props.auth.user.id;
const clientDetails = ref({});

onMounted(() => {
  renderToast(
    getClient(clientId),
    'Carregando dados do cliente...',
    'Dados carregados com sucesso!',
    'Erro ao carregar dados do cliente!',
    (response) => {
      console.log(response.data);
      clientDetails.value = {
        ...response.data,
        telefone: response.data.dddTelefone + response.data.telefone,
        outrosContatos: JSON.parse(response.data.outrosContatos),
      };
      console.log(clientDetails.value);
    },
  );
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Editar Perfil" />
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Seção principal de edição -->
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
          <h2 class="text-lg font-medium text-info dark:text-info/10">Informações do Perfil</h2>
          <ClientProfileForm :client-details="clientDetails" class="mt-6" />
        </div>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
          <UpdatePasswordForm class="max-w-xl" />
        </div>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
          <DeleteUserForm class="max-w-xl" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
