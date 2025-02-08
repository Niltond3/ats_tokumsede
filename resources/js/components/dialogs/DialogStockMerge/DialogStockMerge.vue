<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { getDistributor, listAllDistributors } from '@/services/api/distributors';
import renderToast from '@/components/renderPromiseToast';
import { StringUtil } from '@/util';
import { cn } from '@/lib/utils';
import { Skeleton } from '@/components/ui/skeleton';
import DistributorCombobox from './DistributorCombobox.vue';

const props = defineProps({
  isOpen: { type: Boolean, required: false, default: null },
  toggleDialog: { type: Function, required: false, default: null },
});

const searchTerm = ref('');
const selectedMainDistributor = ref([]);
const selectedSecondaryDistributors = ref([]);
const distributors = ref([]);
const isLoading = ref(true);
const isProcessing = ref(false);

const page = usePage();
const { tipoAdministrador } = page.props.auth.user;

// Filtra distribuidores já selecionados
const availableDistributors = computed(() => {
  return distributors.value.filter(
    (d) =>
      !selectedMainDistributor.value.includes(d.nome) &&
      !selectedSecondaryDistributors.value.includes(d.nome),
  );
});

async function mergeStocks() {
  if (!selectedMainDistributor.value.length || !selectedSecondaryDistributors.value.length) {
    toast.warning('Selecione os distribuidores principal e secundários');
    return;
  }

  isProcessing.value = true;

  const mainId = distributors.value.find((d) => d.nome === selectedMainDistributor.value[0])?.id;
  const secondaryIds = selectedSecondaryDistributors.value
    .map((name) => distributors.value.find((d) => d.nome === name)?.id)
    .filter((id) => id);

  const promise = axios.post('/stock-unions', {
    main_distributor_id: mainId,
    secondary_distributor_ids: secondaryIds,
  });

  renderToast(
    promise,
    'Unificando estoques...',
    'Estoques unificados com sucesso!',
    'Erro ao unificar estoques',
    () => {
      isProcessing.value = false;
      props.toggleDialog();
    },
  );
}

async function getDistributors() {
  isLoading.value = true;
  const promise = listAllDistributors();

  renderToast(
    promise,
    'Carregando distribuidores...',
    'Distribuidores carregados com sucesso!',
    'Erro ao carregar distribuidores',
    (response) => {
      distributors.value = response.data.data.map((d) => ({
        id: d.id,
        nome: StringUtil.utf8Decode(d.nome),
      }));
      isLoading.value = false;
    },
  );
}

const resetValues = () => {
  searchTerm.value = '';
  selectedMainDistributor.value = [];
  selectedSecondaryDistributors.value = [];
  isProcessing.value = false;
};

const handleDialogOpen = (op) => {
  if (!op) {
    resetValues();
    props.toggleDialog();
    return;
  }
  getDistributors();
  props.toggleDialog();
};

getDistributors();
</script>

<template>
  <Dialog :open="props.isOpen" @update:open="handleDialogOpen">
    <slot name="trigger" />
    <DialogContent class="max-w-[90vw] sm:max-w-xl">
      <DialogHeader class="mb-6">
        <DialogTitle class="text-info flex items-center gap-2">
          <i class="ri-git-merge-fill"></i>
          Unificar Estoques
        </DialogTitle>
      </DialogHeader>

      <div class="space-y-6">
        <div v-if="isLoading">
          <Skeleton class="w-full h-14 rounded-lg" />
        </div>
        <div v-else>
          <!-- Distribuidor Principal -->
          <div class="space-y-2">
            <label class="text-sm text-info/80">Distribuidor Principal</label>
            <DistributorCombobox
              v-model="selectedMainDistributor"
              :search-term="searchTerm"
              :distributors="availableDistributors"
              :disabled="isProcessing"
              :max-items="1"
              placeholder="Selecione o distribuidor principal"
              @update:searchTerm="searchTerm = $event"
            />
          </div>

          <!-- Distribuidores Secundários -->
          <div class="space-y-2">
            <label class="text-sm text-info/80">Distribuidores Secundários</label>
            <DistributorCombobox
              v-model="selectedSecondaryDistributors"
              :search-term="searchTerm"
              :distributors="availableDistributors"
              :disabled="isProcessing"
              placeholder="Selecione os distribuidores secundários"
              @update:searchTerm="searchTerm = $event"
            />
          </div>
        </div>

        <div class="flex justify-end gap-2">
          <Button variant="outline" :disabled="isProcessing" @click="props.toggleDialog">
            Cancelar
          </Button>
          <Button :disabled="isProcessing" class="bg-info hover:bg-info/90" @click="mergeStocks">
            <i class="ri-git-merge-line mr-2" />
            Unificar
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
