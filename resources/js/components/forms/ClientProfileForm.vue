<template>
  <div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl text-info/80 font-semibold text-center mb-6">
      Atualizar Perfil do Cliente
    </h2>
    <form class="space-y-6" @submit.prevent="onSubmit">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <FormField v-slot="{ componentField }" name="nome">
          <FormItem class="relative max-h-14">
            <FormControl>
              <Input
                v-bind="componentField"
                type="text"
                placeholder="Seu nome completo"
                :class="inputClass"
              />
            </FormControl>
            <FormLabel :class="labelClass">Nome Completo</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>

        <div class="flex sm:gap-2 w-full sm:flex-row flex-col gap-6">
          <FormField v-slot="{ componentField }" name="sexo">
            <FormItem v-auto-animate class="relative w-full">
              <Popover>
                <PopoverTrigger as-child>
                  <FormControl>
                    <Button
                      variant="outline"
                      class="relative text-slate-500 text-sm w-full !rounded-md font-normal flex justify-start !px-3 max-h-14 min-h-14"
                    >
                      <span v-if="values.sexo === undefined" class="icon text-xl">
                        <GenderlessIcon />
                      </span>
                      <span v-if="values.sexo != undefined && width > 639">{{
                        getSexo.desktop[values.sexo]
                      }}</span>
                      <span v-if="values.sexo != undefined && width < 640">{{
                        getSexo.mobile[values.sexo]
                      }}</span>
                    </Button>
                  </FormControl>
                </PopoverTrigger>
                <PopoverContent class="w-80">
                  <FormControl>
                    <RadioGroup v-bind="componentField">
                      <FormItem class="flex items-center space-x-2 relative">
                        <FormControl>
                          <RadioGroupItem />
                        </FormControl>
                        <FormLabel class="left-4 !m-0 top-1/2 -translate-y-1/2"
                          >Não informar</FormLabel
                        >
                      </FormItem>
                      <FormItem class="flex items-center space-x-2 relative">
                        <FormControl>
                          <RadioGroupItem value="1" />
                        </FormControl>
                        <FormLabel class="left-4 !m-0 top-1/2 -translate-y-1/2"
                          >Másculino</FormLabel
                        >
                      </FormItem>
                      <FormItem class="flex items-center space-x-2 relative">
                        <FormControl>
                          <RadioGroupItem value="2" />
                        </FormControl>
                        <FormLabel class="left-4 !m-0 top-1/2 -translate-y-1/2">Feminino</FormLabel>
                      </FormItem>
                    </RadioGroup>
                  </FormControl>
                </PopoverContent>
              </Popover>
              <FormLabel :class="labelClass"> sexo </FormLabel>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="dataNascimento">
            <FormItem v-auto-animate class="relative w-full">
              <Popover :modal="true">
                <FormControl>
                  <VueDatePicker
                    v-bind="componentField"
                    locale="pt-BR"
                    format="dd/MM/yyyy"
                    :enable-time-picker="false"
                    clearable
                    auto-apply
                    :class="[
                      { 'border-red-500 ring-red-500': hasDateError },
                      '[&_input]:h-14', // Match height with DistributorCombobox
                    ]"
                  />
                </FormControl>
              </Popover>
              <FormLabel :class="cn(labelClass)">
                <span class="sm:hidden">Data de</span>Nascimento
              </FormLabel>
              <FormMessage />
            </FormItem>
          </FormField>
        </div>

        <FormField v-slot="{ componentField }" name="telefone">
          <FormItem class="relative max-h-14">
            <FormControl>
              <Input
                v-mask="['(##) ####-####', '(##) #####-####']"
                v-bind="componentField"
                type="tel"
                placeholder="(xx) xxxx-xxxx"
                :class="inputClass"
              />
            </FormControl>
            <FormLabel :class="labelClass">Telefone</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="email">
          <FormItem class="relative max-h-14">
            <FormControl>
              <Input
                v-bind="componentField"
                type="email"
                placeholder="seuemail@exemplo.com"
                :class="inputClass"
              />
            </FormControl>
            <FormLabel :class="labelClass">E-mail</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="tipoPessoa">
          <FormItem class="relative max-h-14">
            <FormControl>
              <Input
                v-mask="['###.###.###-##', '##.###.###/####-##']"
                v-bind="componentField"
                type="text"
                placeholder="Digite seu documento"
                :class="inputClass"
              />
            </FormControl>
            <FormLabel :class="labelClass">CPF/CNPJ</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>

        <FormField name="outrosContatos">
          <FormItem class="relative">
            <FormControl>
              <div class="flex flex-col group">
                <div
                  class="flex gap-2 border border-slate-300 rounded-md py-1 px-3 max-h-14 min-h-14"
                >
                  <!-- Campo para digitar um novo contato -->
                  <Input
                    v-model="novoContato"
                    type="text"
                    placeholder="Digite um contato adicional"
                    class="border-y-0 border-l-0 !border-r-[1px] rounded-none !ring-0"
                  />
                  <!-- Botão para adicionar o contato -->
                  <Button class="rounded-md px-3 py-1" @click="adicionarContato">Adicionar</Button>
                </div>
                <!-- Lista dos contatos adicionados -->
                <ul
                  class="gap-2 flex-col flex p-2 max-h-48 overflow-y-auto scrollbar mt-2 !scrollbar-w-1.5 !scrollbar-h-1.5 transition-all group-hover:!scrollbar-thumb-slate-200 !scrollbar-thumb-slate-200/0 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2"
                >
                  <li
                    v-for="(contato, index) in outrosContatosList"
                    :key="index"
                    class="flex flex-col gap-2"
                  >
                    <div
                      class="flex justify-between items-center hover:bg-info/10 py-2 px-4 rounded-md text-info"
                    >
                      {{ contato }}
                      <Button
                        type="button"
                        class="py-1 px-2 bg-info/50 hover:bg-info transition-colors rounded-md"
                        @click="removerContato(index)"
                        >Remover</Button
                      >
                    </div>
                    <Separator class="w-full"></Separator>
                  </li>
                </ul>
              </div>
            </FormControl>
            <FormLabel :class="labelClass">Outros Contatos</FormLabel>
            <FormMessage class="absolute -bottom-5 right-3" />
          </FormItem>
        </FormField>
      </div>

      <div class="flex justify-end">
        <button
          type="submit"
          class="inline-flex items-center px-6 py-2 bg-info border border-transparent rounded-md text-white font-medium hover:bg-info-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-info transition-colors"
        >
          Salvar Alterações
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import validator from 'validator';
import { useWindowSize } from '@vueuse/core';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { generatePassword } from '@/util';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';
import { getClientFormat } from '@/Pages/Management/utils';
import { RiGenderlessLine as GenderlessIcon } from 'vue-remix-icons';
import Button from '@/components/Button.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import ptBR from 'date-fns/locale/pt-BR';
import Separator from '../ui/separator/Separator.vue';

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
const emit = defineEmits(['update:birthDatePicker', 'update:generatePassword']);

const { getSexo, getTipoPessoaPayload } = getClientFormat();
const { width } = useWindowSize();

const handleGeneratePassword = () => emit('update:generatePassword', generatePassword());
// Receber os detalhes do cliente via prop
const props = defineProps({
  clientDetails: { type: Object, required: true },
});

const clientData = ref({});
const outrosContatosList = ref([]);
const novoContato = ref('');

// Quando a lista local for atualizada, sincronize com o formulário
watch(outrosContatosList.value, (novoValor) => {
  setFieldValue('outrosContatos', novoValor);
});

// Função para adicionar um contato à lista
function adicionarContato() {
  if (novoContato.value.trim() !== '') {
    outrosContatosList.value.push(novoContato.value.trim());
    novoContato.value = '';
  }
}

// Função para remover um contato da lista
function removerContato(index) {
  outrosContatosList.value.splice(index, 1);
}
// Definição do schema de validação completo
const formSchema = z.object({
  nome: z
    .string({ required_error: 'Informar seu nome é obrigatório' })
    .min(4, { message: 'Nome muito curto' }),
  telefone: z
    .string({ required_error: 'Número de telefone obrigatório' })
    .refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
  senha: z
    .string()
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
    )
    .nullable()
    .optional(),
  confirmSenha: z
    .string()
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
    )
    .nullable()
    .optional(),
  sexo: z.enum(['1', '2']).nullable().optional(),
  dataNascimento: z.preprocess(
    (val) => {
      if (typeof val === 'string') {
        // Aqui você pode também verificar se a string não está vazia
        return new Date(val);
      }
      return val;
    },
    // Depois valida como Date (aceitando null e undefined, se necessário)
    z.date().nullable().optional(),
  ),
  tipoPessoa: z.string().nullable().optional(),
  email: z
    .string({ required_error: 'e-mail obrigatório' })
    .refine(validator.isEmail, { message: 'e-mail inválido' }),
  outrosContatos: z
    .array(z.string().nonempty({ message: 'Contato não pode ser vazio' }))
    .optional(),
});

// Inicializar o formulário com os dados do cliente
const { handleSubmit, setValues, setFieldValue, values, errors } = useForm({
  validationSchema: toTypedSchema(formSchema),
});

watch(
  () => props.clientDetails,
  (newDetails) => {
    if (newDetails) {
      clientData.value = newDetails;
      outrosContatosList.value = newDetails.outrosContatos;
    }
  },
  { immediate: true, deep: true },
);

// Função para submeter o formulário
const onSubmit = handleSubmit((formValues) => {
  // Preparar o payload; aqui você pode também adicionar lógica para senha e confirmSenha, se necessário.
  const { tipoPessoa, documento } = getTipoPessoaPayload(formValues.tipoPessoa);
  const { id } = clientData.value;
  const payload = {
    nome: formValues.nome,
    sexo: formValues.sexo,
    dataNascimento: format(formValues.dataNascimento, 'yyyy-MM-dd', { locale: ptBR }),
    tipoPessoa,
    outrosContatos: formValues.outrosContatos,
    cpf: documento['CPF'],
    cnpj: documento['CNPJ'],
    telefone: formValues.telefone,
    email: formValues.email,
    senha: formValues.senha,
  };

  console.log(payload);
  //   renderToast(
  //     updateClient(clientData.value.id, payload),
  //     'Atualizando dados',
  //     'Perfil atualizado com sucesso!',
  //     'Erro ao atualizar perfil',
  //     (res) => {
  //       console.log(res);
  //     },
  //     (err) => {
  //       console.log(err);
  //     },
  //   );
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
