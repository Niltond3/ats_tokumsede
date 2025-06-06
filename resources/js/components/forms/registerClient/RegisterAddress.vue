<script setup>
import { ref, markRaw, defineComponent, h } from 'vue';
import {
  Stepper,
  StepperItem,
  StepperSeparator,
  StepperTitle,
  StepperTrigger,
} from '@/components/ui/stepper';
import { Form } from '@/components/ui/form';
import Button from '@/components/Button.vue';
import AdressDetails from './components/adressDetails.vue';
import ConfirmClient from './components/confirmClient.vue';
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { BookUser, Check } from 'lucide-vue-next';
import renderToast from '@/components/renderPromiseToast';

const props = defineProps({
  idClient: { type: String, required: false },
  addressDetails: { type: Object, required: false },
});

const emit = defineEmits(['create:success']);

const disabledButton = ref(false);

const formSchema = [
  z.object({
    search: z.string().nullable().optional(),
    cep: z.string().nullable().optional(),
    cidade: z.string(),
    estado: z.string(),
    apelido: z.string().nullable().optional(),
    logradouro: z.string(),
    numero: z.string(),
    bairro: z.string(),
    complemento: z.string().nullable().optional(),
    referencia: z.string().nullable().optional(),
    observacao: z.string().nullable().optional(),
    latitude: z.number(),
    longitude: z.number(),
  }),
  z.object({
    validateInformations: z.boolean().refine((value) => value === true, {
      message: 'Confirme os dados para prosseguir',
    }),
  }),
];

const stepIndex = ref(1);

const steps = [
  {
    step: 1,
    title: 'Endereço',
    icon: BookUser,
  },
  {
    step: 2,
    title: 'Revisão',
    icon: Check,
  },
];

const onSubmit = (values) => {
  console.log(values);
  const payload = {
    idCliente: props.idClient,
    logradouro: values.logradouro || '',
    numero: values.numero || '',
    bairro: values.bairro || '',
    complemento: values.complemento || '',
    cep: values.cep || '',
    cidade: values.cidade || '',
    estado: values.estado || '',
    referencia: values.referencia || '',
    apelido: values.apelido || '',
    observacao: values.observacao || '',
    latitude: values.latitude || 0,
    longitude: values.longitude || 0,
  };
  const promise = props.addressDetails
    ? axios.put(`enderecos/${props.addressDetails.id}`, payload)
    : axios.post('enderecos', payload, { headers: { 'Content-Type': 'application/json' } });

  renderToast(
    promise,
    'Salvando Endereço...',
    'Endereço salvo com sucesso!',
    'Ocorreu um erro ao salvar o endereço!',
    () => emit('create:success'),
  );
};

const handleUpdateAddress = (addressValue, setValues) => {
  console.log(addressValue);
  setValues({
    search: addressValue.search && addressValue.search,
    cep: addressValue.cep && addressValue.cep,
    cidade: addressValue.cidade && addressValue.cidade,
    estado: addressValue.estado && addressValue.estado,
    logradouro: addressValue.logradouro && addressValue.logradouro,
    numero: addressValue.numero && addressValue.numero,
    bairro: addressValue.bairro && addressValue.bairro,
    latitude: addressValue.latitude && addressValue.latitude,
    longitude: addressValue.longitude && addressValue.longitude,
  });
};
</script>

<template>
  <Form
    v-slot="{ meta, validate, values, setValues }"
    as=""
    keep-values
    :validation-schema="toTypedSchema(formSchema[stepIndex - 1])"
    :initial-values="addressDetails || {}"
  >
    <Stepper
      v-slot="{ isNextDisabled, isPrevDisabled, nextStep, prevStep }"
      v-model="stepIndex"
      class="block w-full"
    >
      <form
        form
        @submit="
          (event) => {
            event.preventDefault();
            validate();

            if (stepIndex === steps.length && meta.valid) onSubmit(values);
          }
        "
      >
        <div class="flex w-full flex-start gap-2">
          <StepperItem
            v-for="step in steps"
            :key="step.step"
            v-slot="{ state }"
            class="relative flex w-full flex-col items-center justify-center"
            :step="step.step"
          >
            <StepperSeparator
              v-if="step.step !== steps[steps.length - 1].step"
              class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-info"
            />

            <StepperTrigger as-child>
              <Button
                :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                size="icon"
                class="z-10 rounded-full shrink-0 transition-all"
                :class="[
                  state === 'active' &&
                    'hover:bg-info/90 bg-info ring-2 ring-offset-2 ring-offset-info',
                ]"
                :disabled="state !== 'completed' && !meta.valid"
              >
                <component :is="step.icon" class="w-4 h-4" />
              </Button>
            </StepperTrigger>

            <div class="pb-5 flex flex-col items-center text-center">
              <StepperTitle
                :class="[state === 'active' && '!text-info translate-y-3 scale-125']"
                class="text-slate-300 !text-[12px] font-semibold transition-all lg:text-base"
              >
                {{ step.title }}
              </StepperTitle>
            </div>
          </StepperItem>
        </div>
        <div>
          <AdressDetails
            v-if="stepIndex === 1"
            @update:address-value="(addressValue) => handleUpdateAddress(addressValue, setValues)"
          />

          <ConfirmClient v-if="stepIndex === 2" :values="values" />
        </div>
        <div class="flex items-center justify-between mt-4">
          <Button
            class="disabled:cursor-not-allowed text-info/75 hover:text-info transition-colors"
            :disabled="isPrevDisabled"
            variant="outline"
            size="sm"
            @click="prevStep()"
          >
            Voltar
          </Button>
          <div class="flex items-center gap-3">
            <Button
              v-if="stepIndex !== 2"
              :type="meta.valid ? 'button' : 'submit'"
              :disabled="isNextDisabled"
              size="sm"
              @click="meta.valid && nextStep()"
            >
              Próximo
            </Button>
            <Button
              v-if="stepIndex === 2"
              size="sm"
              type="submit"
              :disabled="disabledButton"
              class="disabled:cursor-not-allowed"
            >
              {{ addressDetails ? 'Atualiazar' : 'Cadastrar' }}
            </Button>
          </div>
        </div>
      </form>
    </Stepper>
  </Form>
</template>
