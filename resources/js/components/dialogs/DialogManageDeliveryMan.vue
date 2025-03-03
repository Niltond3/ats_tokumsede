<template>
  <Dialog :open="isOpen" @update:open="handleDialogOpen">
    <!-- Dialog trigger for managing deliverymen -->
    <DialogTrigger icon="ri-truck-line" title="Entregadores" />
    <DialogContent class="flex gap-4 flex-col">
      <DialogHeader>
        <DialogTitle class="flex gap-3 text-lg text-info items-center">
          <i class="ri-truck-line"></i>
          <span>Gerenciar Entregadores</span>
        </DialogTitle>
        <DialogDescription class="py-2">
          Cadastrar, atualizar, excluir e atribuir entregadores.
        </DialogDescription>
      </DialogHeader>

      <!-- Filter Section -->
      <div class="flex flex-col gap-4">
        <!-- Input field for filtering by deliveryman name or vehicle plate -->
        <div class="relative">
          <i class="ri-search-2-fill absolute left-2 top-1/2 -translate-y-1/2 text-info"></i>
          <Input
            v-model="filterText"
            type="text"
            class="pl-8"
            placeholder="Pesquise por nome ou placa do veículo"
          />
        </div>
        <!-- Distributor Filter Tabs -->
        <div
          class="flex gap-2 max-w-full overflow-x-scroll scrollbar scrollbar-corner-rounded !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2 border-slate-00 mb-3 pb-2"
        >
          <!-- "Todos" button: resets distributor filter when clicked -->
          <Button
            class="px-2 py-0.5 text-info"
            :class="{ 'bg-primary text-white': filterDistributorId === null }"
            :variant="filterDistributorId === null ? 'default' : 'primary'"
            @click="filterDistributorId = null"
          >
            Todos
          </Button>
          <!-- Loop through the sorted distributors to create a tab for each -->
          <Button
            v-for="distribuidor in sortedDistribuidores"
            :key="distribuidor.id"
            class="px-2 py-0.5 text-info"
            :class="{ 'bg-primary text-white': filterDistributorId === distribuidor.id }"
            :variant="filterDistributorId === distribuidor.id ? 'default' : 'primary'"
            @click="filterDistributorId = distribuidor.id"
          >
            <!-- Use helper method to remove "TôKumSede " prefix -->
            {{ formatDistributorName(distribuidor.nome) }}
          </Button>
        </div>
      </div>
      <!-- Container to display deliverymen list -->
      <div class="border-y py-2 overflow-hidden border-slate-200 min-h-56">
        <!-- List of deliverymen (filtered) -->
        <ul
          class="overflow-y-auto max-h-64 scrollbar scrollbar-corner-rounded !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2"
        >
          <!-- Iterate over the filtered deliverymen list -->
          <li
            v-for="deliveryman in filteredDeliverymen"
            :key="deliveryman.id"
            class="group p-2 rounded hover:bg-info/80 transition-all text-info text-base flex gap-2 items-center group/line duration-300"
          >
            <!-- Button container with a right border for separation -->
            <div class="mr-auto border-r border-slate-300 pr-3 flex gap-2">
              <!-- Edit button styled to match the reference element -->
              <button
                class="relative !size-10 text-2xl shadow-md rounded-full transition-all group-hover/line:bg-white hover:shadow-lg text-info/60 hover:text-info/100"
                @click="editDeliveryman(deliveryman)"
              >
                <i
                  class="ri-pencil-fill text-sm pointer-events-none select-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                ></i>
              </button>
            </div>
            <!-- Deliveryman information container -->
            <div class="flex flex-col group-hover:text-white w-full">
              <!-- Deliveryman name -->
              <span class="text-base">{{ deliveryman.nome }}</span>
              <!-- Deliveryman phone -->
              <span class="text-sm text-info/80 group-hover:text-white">{{
                deliveryman.telefone
              }}</span>
              <!-- Vehicle plate -->
              <span class="text-sm text-info/80 group-hover:text-white"
                >Placa: {{ deliveryman.placaVeiculo }}</span
              >
              <!-- Distributor info with formatted name -->
              <span class="text-sm text-info/80 group-hover:text-white">
                Distribuidor:
                {{
                  deliveryman.distribuidor
                    ? formatDistributorName(deliveryman.distribuidor.nome)
                    : 'Não atribuído'
                }}
              </span>
            </div>
            <!-- Button container with a left border for separation -->
            <div class="ml-auto border-l border-slate-300 pl-3 flex gap-2">
              <!-- Delete button styled to match the reference element -->
              <button
                variant="destructive"
                class="relative !size-10 text-2xl shadow-md rounded-full transition-all group-hover/line:bg-white hover:shadow-lg text-info/60 hover:text-info/100"
                @click="deleteDeliveryman(deliveryman.id)"
              >
                <i
                  class="ri-delete-bin-6-fill text-sm pointer-events-none select-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                ></i>
              </button>
            </div>
          </li>
        </ul>
      </div>
      <!-- Form for creating or editing a deliveryman -->
      <Form
        v-slot="{ validate, values, setFieldValue }"
        :key="
          deliverymanDetails.id ? `edit-deliveryman-${deliverymanDetails.id}` : 'new-deliveryman'
        "
        as=""
        :validation-schema="toTypedSchema(formSchema)"
        :initial-values="deliverymanDetails || {}"
      >
        <form @submit="(event) => handleSubmit(event, values, validate)">
          <div class="flex flex-col gap-5">
            <!-- Field for deliveryman name -->
            <FormField v-slot="{ componentField }" name="nome">
              <FormItem>
                <FormControl>
                  <Input v-bind="componentField" type="text" placeholder="Nome do entregador" />
                </FormControl>
                <FormLabel class="-top-6 peer-placeholder-shown:-top-4">Nome</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Field for deliveryman phone -->
            <FormField v-slot="{ componentField }" name="telefone">
              <FormItem>
                <FormControl>
                  <Input v-bind="componentField" type="text" placeholder="Telefone" />
                </FormControl>
                <FormLabel>Telefone</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Field for vehicle plate -->
            <FormField v-slot="{ componentField }" name="placaVeiculo">
              <FormItem>
                <FormControl>
                  <Input v-bind="componentField" type="text" placeholder="Placa do veículo" />
                </FormControl>
                <FormLabel>Placa do Veículo</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Field for distributor selection -->
            <FormField v-slot="{ field }" name="idDistribuidor">
              <FormItem>
                <FormControl>
                  <select
                    :value="field.value"
                    class="border rounded-md border-slate-300 py-1 focus:border-info/60 focus:ring-info/60 focus-visible:!ring-info/60 focus-visible:border-info/60 pr-8 pl-2"
                    @change="
                      (e) =>
                        setFieldValue(
                          'idDistribuidor',
                          e.target.value ? Number(e.target.value) : undefined,
                        )
                    "
                  >
                    <option value="" class="text-info/60">Selecione um distribuidor</option>
                    <!-- Loop through all distributors for the select options -->
                    <option
                      v-for="distribuidor in distribuidoresList"
                      :key="distribuidor.id"
                      :value="distribuidor.id"
                    >
                      {{ formatDistributorName(distribuidor.nome) }}
                    </option>
                  </select>
                </FormControl>
                <FormLabel>Distribuidor</FormLabel>
                <FormMessage />
              </FormItem>
            </FormField>

            <!-- Submit button for the form -->
            <div>
              <Button type="submit" size="sm" :disabled="disabledButton">
                {{ deliverymanDetails.id ? 'Atualizar' : 'Cadastrar' }}
              </Button>
            </div>
          </div>
        </form>
      </Form>
    </DialogContent>
  </Dialog>
</template>

<script setup>
// Importing necessary functions and components from Vue and external libraries
import { ref, computed, onMounted, watch } from 'vue';
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
import { useDeliverymanManagement } from '@/composables/useDeliverymanManagement';
import { useDistributorFilter } from '@/composables/useDistributorFilter';

// Destructure reactive variables and functions from the deliveryman management composable
const {
  isOpen,
  deliverymanDetails,
  deliverymenList,
  distribuidoresList,
  disabledButton,
  formSchema,
  handleDialogOpen,
  handleSubmit,
  fetchDeliverymen,
  deleteDeliveryman,
  editDeliveryman,
} = useDeliverymanManagement();

// Reactive variable for the text filter (applies to both name and vehicle plate)
const filterText = ref('');

// Use the distributor filter composable to handle distributor filtering logic
const { filterDistributorId, sortedDistribuidores, formatDistributorName } =
  useDistributorFilter(distribuidoresList);

// Computed property that filters the deliverymen list based on the filter criteria
const filteredDeliverymen = computed(() => {
  return deliverymenList.value.filter((deliveryman) => {
    // Convert the filter text to lowercase for a case-insensitive match
    const text = filterText.value.toLowerCase();
    // Check if the deliveryman's name or vehicle plate contains the filter text
    const matchesText =
      !text ||
      (deliveryman.nome && deliveryman.nome.toLowerCase().includes(text)) ||
      (deliveryman.placaVeiculo && deliveryman.placaVeiculo.toLowerCase().includes(text));

    // Check if the distributor filter is applied; if not, accept all distributors
    const matchesDistributor =
      filterDistributorId.value === null ||
      (deliveryman.distribuidor && deliveryman.distribuidor.id === filterDistributorId.value) ||
      deliveryman.idDistribuidor === filterDistributorId.value;

    // Only include deliverymen that match both filters
    return matchesText && matchesDistributor;
  });
});

// Fetch the list of deliverymen when the component is mounted
onMounted(() => {
  fetchDeliverymen();
});
</script>
