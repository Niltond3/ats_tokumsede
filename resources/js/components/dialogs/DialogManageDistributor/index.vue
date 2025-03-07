<template>
  <Dialog :open="isOpen" @update:open="handleDialogOpen">
    <DialogTrigger icon="ri-building-4-line" title="Distribuidores" />

    <DialogContent class="flex flex-col gap-4" @interact-outside="handleDialogOutsideInteract">
      <DialogHeader>
        <DialogTitle class="flex gap-3 text-lg text-info items-center">
          <i class="ri-building-4-line"></i>
          <span>Gerenciar Distribuidores</span>
        </DialogTitle>
        <DialogDescription class="py-2">
          Cadastre, atualize, exclua e gerencie os distribuidores.
        </DialogDescription>
      </DialogHeader>

      <!-- Lista de distribuidores para administradores que não são Distribuidor -->
      <template v-if="tipoAdministrador !== 'Distribuidor'">
        <DistributorManagementList
          :distributors-list="distributorsList"
          :edit-distributor="editDistributor"
          :delete-distributor="deleteDistributor"
        />
      </template>

      <!-- Formulário multi‐etapas -->
      <Form
        ref="formRef"
        v-slot="{ meta, values, setValues }"
        :key="
          distributorDetails.id ? 'edit-distributor-' + distributorDetails.id : 'new-distributor'
        "
        keep-values
        :initial-values="distributorDetails || {}"
      >
        <Stepper
          v-slot="{ isPrevDisabled, nextStep, prevStep }"
          v-model="stepIndex"
          class="block w-full"
        >
          <form @submit="(event) => handleFormSubmit(event, meta, values)">
            <!-- Cabeçalho do Stepper -->
            <div class="flex w-full gap-2">
              <StepperItem
                v-for="step in steps"
                :key="step.step"
                v-slot="{ state }"
                class="relative flex w-full flex-col items-center justify-center"
                :step="step.step"
              >
                <StepperSeparator
                  v-if="step.step !== steps[steps.length - 1].step"
                  class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted"
                />
                <StepperTrigger as-child>
                  <Button
                    :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                    size="icon"
                    class="z-10 rounded-full shrink-0 transition-all"
                    :class="state === 'active' && 'bg-info ring-2 ring-offset-2'"
                  >
                    <component :is="step.icon" class="w-4 h-4" />
                  </Button>
                </StepperTrigger>
                <div class="pt-5 text-center">
                  <StepperTitle class="text-sm font-semibold text-info">{{
                    step.title
                  }}</StepperTitle>
                </div>
              </StepperItem>
            </div>

            <!-- Conteúdo de cada etapa -->
            <div class="mt-4">
              <DistributorBasicDetails v-if="stepIndex === 1" :values="values" />
              <DistributorAddressDetails
                v-if="stepIndex === 2"
                :values="values"
                @update:address-value="
                  (addressValue) => handleUpdateAddress(addressValue, setValues)
                "
              />
              <DistributorReview v-if="stepIndex === 3" :values="values" />
            </div>

            <!-- Botões de navegação -->
            <div class="flex items-center justify-between mt-4">
              <Button variant="outline" size="sm" :disabled="isPrevDisabled" @click="prevStep()">
                Voltar
              </Button>
              <div class="flex items-center gap-3">
                <Button
                  v-if="stepIndex !== steps.length"
                  size="sm"
                  @click="handleNext(values, nextStep)"
                >
                  Próximo
                </Button>
                <Button v-else size="sm" type="submit" :disabled="disabledButton"> Salvar </Button>
              </div>
            </div>
          </form>
        </Stepper>
      </Form>
    </DialogContent>
  </Dialog>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog';
import DialogTrigger from '@/components/dialogs/DialogTrigger.vue';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import {
  Stepper,
  StepperItem,
  StepperSeparator,
  StepperTitle,
  StepperTrigger,
} from '@/components/ui/stepper';
import { Input } from '@/components/ui/input';

import DistributorBasicDetails from './components/DistributorBasicDetails.vue';
import DistributorAddressDetails from './components/DistributorAddressDetails.vue';
import DistributorReview from './components/DistributorReview.vue';

import { useDistributorManagement } from '@/composables/useDistributorManagement';
import * as z from 'zod';
import DistributorManagementList from './components/DistributorManagementList.vue';

const {
  isOpen,
  handleDialogOpen,
  formSchemas,
  fetchDistributor,
  fetchDistributors,
  disabledButton,
  deleteDistributor,
  editDistributor,
  handleSubmit,
  distributorDetails,
  distributorsList,
} = useDistributorManagement();

const formRef = ref(null);

const page = usePage();
const { tipoAdministrador, idDistribuidor: userDistributorId } = page.props.auth.user;

const stepIndex = ref(1);
const steps = [
  { step: 1, title: 'Dados', icon: 'User' },
  { step: 2, title: 'Endereço', icon: 'MapPin' },
  { step: 3, title: 'Revisão', icon: 'Check' },
];

/**
 * Ao clicar em "Próximo", validamos somente os campos do passo atual
 * utilizando a schema específica (formSchemas é um array com 3 schemas).
 */
const handleNext = async (values, nextStep) => {
  const currentSchema = formSchemas[stepIndex.value - 1];
  const result = currentSchema.safeParse(values);
  if (result.success) {
    nextStep();
  } else {
    // Aqui você pode integrar a exibição dos erros se desejar
    console.log(result.error.flatten());
  }
};

const handleFormSubmit = async (event, meta, values) => {
  event.preventDefault();
  // Se estivermos no último passo e a validação geral for válida, submete o form
  if (stepIndex.value === steps.length && meta.valid) {
    await handleSubmit(event, values, () => Promise.resolve({ valid: true }));
  }
};

/**
 * Quando o diálogo é aberto, se estivermos em modo de edição,
 * reinicia o stepIndex e atualiza o form com os dados atuais.
 */
watch(isOpen, (newVal) => {
  if (newVal) {
    stepIndex.value = 1;
    if (distributorDetails.value && formRef.value) {
      formRef.value.resetForm({ values: distributorDetails.value });
    }
  }
});

const handleDialogOutsideInteract = (event) => {
  const classes = [];
  event.composedPath().forEach((element) => {
    if (element.classList) {
      classes.push(Array.from(element.classList));
    }
  });
  if (classes.join(' -').includes('pac-container')) event.preventDefault();
};

const handleUpdateAddress = (addressValue, setValues) =>
  setValues({
    search: addressValue.search && addressValue.search,
    cep: addressValue.cep && addressValue.cep,
    cidade: addressValue.cidade && addressValue.cidade,
    estado: addressValue.estado && addressValue.estado,
    logradouro: addressValue.logradouro && addressValue.logradouro,
    numero: addressValue.numero && addressValue.numero,
    bairro: addressValue.bairro && addressValue.bairro,
  });

onMounted(() => {
  if (tipoAdministrador !== 'Distribuidor') {
    fetchDistributors();
  } else {
    fetchDistributor(userDistributorId);
  }
});
</script>
