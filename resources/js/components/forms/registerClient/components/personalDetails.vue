<script setup>
import { computed } from 'vue';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { useWindowSize } from '@vueuse/core';
import { getClientFormat } from '@/Pages/Management/utils';
import {
  RiLoginBoxLine as LoginIcon,
  RiGenderlessLine as GenderlessIcon,
  RiCalendarLine as CalendarIcon,
} from 'vue-remix-icons';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import { cn } from '@/lib/utils';
import { format, parseISO } from 'date-fns';
import ptBR from 'date-fns/locale/pt-BR';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Input } from '@/components/ui/input';
import DatePicker from './datePicker.vue';
import Button from '@/components/Button.vue';
import { generatePassword } from '@/util';
import DialogOthersContacts from './DialogOthersContacts.vue';

const { values } = defineProps({ values: Object });

const emit = defineEmits(['update:birthDatePicker', 'update:generatePassword']);

const { width } = useWindowSize();

const { getSexo } = getClientFormat();

const formatMask = width > 639 ? "dd'º de' MMM',' yyyy" : 'dd/MM/yyyy';

const dateToIso = (date) => parseISO(date.toString());

const getDataFormat = (date) => format(dateToIso(date), formatMask, { locale: ptBR });

const value = computed({
  get: () => (values.dataNascimento ? parseDate(values.dataNascimento) : undefined),
  set: (val) => val,
});

const handleBirthDateChange = (value) => emit('update:birthDatePicker', value);

const handleGeneratePassword = () => emit('update:generatePassword', generatePassword());

const inputClass =
  'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600';

const labelClass =
  'absolute -top-4 text-info/50 peer-placeholder-shown:text-info text-[13px] px-1 left-px bg-white';
</script>

<template>
  <section
    class="flex flex-col gap-4 mt-4 space-y-6 sm:grid sm:grid-cols-6 sm:gap-4 sm:space-y-0 relative"
  >
    <FormField v-slot="{ componentField }" name="nome">
      <FormItem v-auto-animate class="relative sm:col-span-5">
        <FormControl>
          <Input
            v-bind="componentField"
            autocomplete="name"
            type="text"
            placeholder="Nome completo"
            :class="cn(inputClass)"
          />
        </FormControl>
        <FormLabel :class="cn(labelClass)"> Nome </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="sexo">
      <FormItem v-auto-animate class="relative sm:col-span-1">
        <Popover>
          <PopoverTrigger as-child>
            <FormControl>
              <Button
                variant="outline"
                class="relative text-slate-500 text-sm !p-2 min-h-[22px] !rounded-sm font-normal flex justify-start !px-3"
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
                <FormItem class="flex items-center space-x-2">
                  <FormControl>
                    <RadioGroupItem />
                  </FormControl>
                  <FormLabel>Não informar</FormLabel>
                </FormItem>
                <FormItem class="flex items-center space-x-2">
                  <FormControl>
                    <RadioGroupItem value="1" />
                  </FormControl>
                  <FormLabel>Másculino</FormLabel>
                </FormItem>
                <FormItem class="flex items-center space-x-2">
                  <FormControl>
                    <RadioGroupItem value="2" />
                  </FormControl>
                  <FormLabel>Feminino</FormLabel>
                </FormItem>
              </RadioGroup>
            </FormControl>
          </PopoverContent>
        </Popover>
        <FormLabel
          class="absolute -top-4 text-info/50 peer-placeholder-shown:text-info text-[13px] left-1 bg-white"
        >
          <span class="sm:hidden">selecione seu </span>sexo
        </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <div class="relative sm:col-span-3">
      <DialogOthersContacts class="right-0 sm:-right-3"></DialogOthersContacts>
      <FormField v-slot="{ componentField }" name="telefone">
        <FormItem v-auto-animate class="relative">
          <FormControl>
            <Input
              v-mask="['(##) ####-####', '(##) #####-####']"
              v-bind="componentField"
              autocomplete="username"
              type="text"
              placeholder="Número de telefone"
              :class="cn(inputClass)"
            />
          </FormControl>
          <FormLabel :class="cn(labelClass)"> Telefone </FormLabel>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>
    <FormField v-slot="{ componentField }" name="tipoPessoa">
      <FormItem v-auto-animate class="relative sm:col-span-3">
        <FormControl>
          <Input
            v-mask="['###.###.###-##', '##.###.###/####-##']"
            v-bind="componentField"
            autocomplete="username"
            type="text"
            placeholder="CPF ou CNPJ"
            label="CPF/CNPJ"
            :class="cn(inputClass)"
          />
        </FormControl>
        <FormLabel :class="cn(labelClass)"> CPF/CNPJ </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="email">
      <FormItem v-auto-animate class="relative sm:col-span-4">
        <FormControl>
          <Input
            v-bind="componentField"
            autocomplete="username"
            type="email"
            placeholder="E-mail válido"
            :class="cn(inputClass)"
          />
        </FormControl>
        <FormLabel :class="cn(labelClass)"> email </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField name="dataNascimento">
      <FormItem v-auto-animate class="relative flex flex-col sm:col-span-2">
        <Popover>
          <PopoverTrigger as-child>
            <FormControl>
              <Button
                variant="outline"
                :class="
                  cn(
                    'text-slate-500 text-sm !p-2 min-h-[22px] !rounded-sm font-normal flex justify-start !px-3',
                    !value && 'text-muted-foreground',
                  )
                "
              >
                <span>
                  {{ values.dataNascimento && getDataFormat(values.dataNascimento) }}
                </span>
                <span v-if="width < 640">Selecione uma data</span>
                <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </FormControl>
          </PopoverTrigger>
          <PopoverContent class="p-0">
            <DatePicker
              v-model="value"
              calendar-label="Data de Nascimento"
              initial-focus
              :min-value="new CalendarDate(1900, 1, 1)"
              :max-value="today(getLocalTimeZone())"
              @update:model-value="(v) => handleBirthDateChange(v)"
            />
          </PopoverContent>
        </Popover>
        <FormLabel :class="cn(labelClass)">
          <span class="sm:hidden">Data de</span>Nascimento
        </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="senha">
      <FormItem v-auto-animate class="relative sm:col-span-3">
        <div class="relative">
          <FormControl>
            <Input
              v-bind="componentField"
              autocomplete="new-password"
              type="password"
              placeholder="Senha"
              :class="cn(inputClass)"
            />
          </FormControl>
          <FormLabel :class="cn(labelClass, '-top-2')"> Senha </FormLabel>
          <Button
            class="rounded-none absolute icon text-xl hover:bg-transparent text-info/60 hover:text-info transition-colors top-1/2 -translate-y-1/2 border-l-input border-l-[1px] py-1 px-2 border-t-0 border-b-0 border-r-0 right-0 bg-transparent outline-none ring-transparent !m-0"
            @click="handleGeneratePassword"
          >
            <i class="ri-dice-line"></i>
          </Button>
        </div>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="confirmSenha">
      <FormItem v-auto-animate class="relative sm:col-span-3">
        <FormControl>
          <Input
            v-bind="componentField"
            autocomplete="new-password"
            type="password"
            placeholder="Confirme sua senha"
            :class="cn(inputClass)"
          />
        </FormControl>
        <FormLabel :class="cn(labelClass)"> Confirmação de senha </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
  </section>
</template>
