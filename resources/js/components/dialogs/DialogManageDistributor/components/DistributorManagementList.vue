<template>
  <div>
    <div class="flex flex-col gap-4">
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
    <div class="border-y py-2 overflow-hidden border-slate-200 min-h-56">
      <ul class="overflow-y-auto max-h-64 scrollbar">
        <li
          v-for="distributor in filteredDistributors"
          :key="distributor.id"
          class="group p-2 rounded hover:bg-info/80 transition-all text-info text-base flex gap-2 items-center"
        >
          <div class="mr-auto border-r border-slate-300 pr-3 flex gap-2">
            <button
              class="relative !size-10 text-2xl shadow-md rounded-full transition-all bg-white"
              @click="handleEditDistributor(distributor)"
            >
              <i
                class="ri-pencil-fill text-sm absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2"
              ></i>
            </button>
          </div>
          <div class="flex flex-col w-full">
            <span class="text-base group-hover:text-white">{{ distributor.nome }}</span>
            <span class="text-sm text-info/80 group-hover:text-white/80"
              >CNPJ: {{ distributor.cnpj }}</span
            >
            <span class="text-sm text-info/80 group-hover:text-white/80"
              >Email: {{ distributor.email }}</span
            >
            <span class="text-sm text-info/80 group-hover:text-white/80"
              >Telefone: ({{ distributor.dddTelefone }}) {{ distributor.telefonePrincipal }}</span
            >
          </div>
          <div class="ml-auto border-l border-slate-300 pl-3 flex gap-2">
            <DialogConfirmAction
              :dropdown="false"
              dialog-title="Deletar Distribuidor"
              variant="danger"
              @on:confirm="deleteDistributor(distributor.id)"
            >
              <button
                class="relative !size-10 text-2xl shadow-md rounded-full transition-all bg-white"
              >
                <i
                  class="ri-delete-bin-6-fill text-sm absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2"
                />
              </button>
            </DialogConfirmAction>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Input } from '@/components/ui/input';
import DialogConfirmAction from '@/components/dialogs/DialogConfirmAction.vue';

const props = defineProps({
  editDistributor: {
    type: Function,
    required: true,
  },
  deleteDistributor: {
    type: Function,
    required: true,
  },
  distributorsList: {
    type: Array,
    required: true,
  },
});

const filterText = ref('');

const filteredDistributors = computed(() => {
  const text = filterText.value.toLowerCase();
  return props.distributorsList.filter(
    (d) =>
      (d.nome && d.nome.toLowerCase().includes(text)) ||
      (d.cnpj && d.cnpj.toLowerCase().includes(text)),
  );
});

/**
 * Ao clicar em editar, carregamos os dados do distribuidor,
 * reiniciamos o stepIndex e atualizamos os valores do form
 * utilizando resetForm para pré-popular todos os campos.
 */
const handleEditDistributor = async (distributor) => {
  await props.editDistributor(distributor);
  stepIndex.value = 1;
  if (formRef.value) {
    formRef.value.resetForm({ values: distributorDetails.value });
  }
};
</script>
