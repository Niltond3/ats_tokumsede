<template>
  <!-- Componente Dialog para gerenciar distribuidores -->
  <Dialog :open="isOpen" @update:open="handleDialogOpen">
    <!-- Botão para abrir o diálogo com ícone e título -->
    <DialogTrigger icon="ri-building-4-line" title="Distribuidores" />

    <!-- Conteúdo do diálogo -->
    <DialogContent class="flex gap-4 flex-col">
      <!-- Cabeçalho do diálogo com título e descrição -->
      <DialogHeader>
        <DialogTitle class="flex gap-3 text-lg text-info items-center">
          <i class="ri-building-4-line"></i>
          <span>Gerenciar Distribuidores</span>
        </DialogTitle>
        <DialogDescription class="py-2">
          Cadastrar, atualizar, excluir e gerenciar distribuidores.
        </DialogDescription>
      </DialogHeader>

      <!-- Seção de filtros -->
      <div class="flex flex-col gap-4">
        <!-- Campo de busca para filtrar por nome ou CNPJ -->
        <div class="relative">
          <i class="ri-search-2-fill absolute left-2 top-1/2 -translate-y-1/2 text-info"></i>
          <Input
            v-model="filterText"
            type="text"
            class="pl-8"
            placeholder="Pesquise por nome ou CNPJ"
          />
        </div>
      </div>

      <!-- Lista de distribuidores -->
      <div class="border-y py-2 overflow-hidden border-slate-200 min-h-56">
        <ul
          class="overflow-y-auto max-h-64 scrollbar scrollbar-corner-rounded !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2"
        >
          <li
            v-for="distributor in filteredDistributors"
            :key="distributor.id"
            class="group p-2 rounded hover:bg-info/80 transition-all text-info text-base flex gap-2 items-center group/line duration-300"
          >
            <!-- Botão para editar o distribuidor -->
            <div class="mr-auto border-r border-slate-300 pr-3 flex gap-2">
              <button
                class="relative !size-10 text-2xl shadow-md rounded-full transition-all group-hover/line:bg-white hover:shadow-lg text-info/60 hover:text-info/100"
                @click="editDistributor(distributor)"
              >
                <i
                  class="ri-pencil-fill text-sm pointer-events-none select-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                ></i>
              </button>
            </div>
            <!-- Informações do distribuidor -->
            <div class="flex flex-col group-hover:text-white w-full">
              <span class="text-base">{{ distributor.nome }}</span>
              <span class="text-sm text-info/80 group-hover:text-white"
                >CNPJ: {{ distributor.cnpj }}</span
              >
              <span class="text-sm text-info/80 group-hover:text-white"
                >Email: {{ distributor.email }}</span
              >
              <span class="text-sm text-info/80 group-hover:text-white"
                >Telefone: {{ distributor.telefonePrincipal }}</span
              >
            </div>
            <!-- Botão para excluir o distribuidor -->
            <div class="ml-auto border-l border-slate-300 pl-3 flex gap-2">
              <button
                variant="destructive"
                class="relative !size-10 text-2xl shadow-md rounded-full transition-all group-hover/line:bg-white hover:shadow-lg text-info/60 hover:text-info/100"
                @click="deleteDistributor(distributor.id)"
              >
                <i
                  class="ri-delete-bin-6-fill text-sm pointer-events-none select-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                ></i>
              </button>
            </div>
          </li>
        </ul>
      </div>

      <!-- Formulário para criação/edição de distribuidor -->
      <Form
        v-slot="{ validate, values, setFieldValue }"
        :key="
          distributorDetails.id ? `edit-distributor-${distributorDetails.id}` : 'new-distributor'
        "
        as=""
        :validation-schema="toTypedSchema(formSchema)"
        :initial-values="distributorDetails || {}"
      >
        <form @submit="(event) => handleSubmit(event, values, validate)">
          <div class="flex flex-col gap-5">
            <!-- Campo: Nome do distribuidor -->
            <FormField v-slot="{ componentField }" name="nome">
              <FormItem>
                <FormControl>
                  <Input v-bind="componentField" type="text" placeholder="Nome do distribuidor" />
                </FormControl>
                <FormLabel class="-top-6 peer-placeholder-shown:-top-4">Nome</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Campo: CNPJ -->
            <FormField v-slot="{ componentField }" name="cnpj">
              <FormItem>
                <FormControl>
                  <Input v-bind="componentField" type="text" placeholder="CNPJ" />
                </FormControl>
                <FormLabel>CNPJ</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Campo: Email -->
            <FormField v-slot="{ componentField }" name="email">
              <FormItem>
                <FormControl>
                  <Input v-bind="componentField" type="email" placeholder="Email" />
                </FormControl>
                <FormLabel>Email</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Campo: Telefone -->
            <FormField v-slot="{ componentField }" name="telefonePrincipal">
              <FormItem>
                <FormControl>
                  <Input
                    v-mask="['(##) ####-####', '(##) #####-####']"
                    v-bind="componentField"
                    type="text"
                    placeholder="Telefone"
                  />
                </FormControl>
                <FormLabel>Telefone</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Botão de envio do formulário -->
            <div>
              <Button type="submit" size="sm" :disabled="disabledButton">
                {{ distributorDetails.id ? 'Atualizar' : 'Cadastrar' }}
              </Button>
            </div>
          </div>
        </form>
      </Form>
    </DialogContent>
  </Dialog>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog';
import DialogTrigger from '@/components/dialogs/DialogTrigger.vue';
import {
  Form,
  FormField,
  FormItem,
  FormLabel,
  FormControl,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { useDistributorManagement } from '@/composables/useDistributorManagement';

const {
  isOpen,
  distributorDetails,
  distributorsList,
  disabledButton,
  formSchema,
  handleDialogOpen,
  handleSubmit: originalHandleSubmit,
  fetchDistributors,
  deleteDistributor,
  editDistributor,
} = useDistributorManagement();

const filterText = ref('');

const filteredDistributors = computed(() => {
  const text = filterText.value.toLowerCase();
  return distributorsList.value.filter((distributor) => {
    return (
      !text ||
      (distributor.nome && distributor.nome.toLowerCase().includes(text)) ||
      (distributor.cnpj && distributor.cnpj.toLowerCase().includes(text))
    );
  });
});

const handleSubmitWrapper = (event, values, validate) => {
  return originalHandleSubmit(event, values, validate);
};
const handleSubmit = handleSubmitWrapper;

onMounted(() => {
  fetchDistributors();
});
</script>
