<script setup>
import { computed } from 'vue';
import { CalendarDate, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { useWindowSize } from '@vueuse/core';
import {
  RiLoginBoxLine as LoginIcon,
  RiGenderlessLine as GenderlessIcon,
  RiCalendarLine as CalendarIcon,
} from 'vue-remix-icons';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import {ClientUtil} from '@/util';
import { cn } from '@/lib/utils';
import { format, parseISO } from 'date-fns';
import ptBR from 'date-fns/locale/pt-BR';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Input } from '@/components/ui/input';
import DatePicker from './datePicker.vue';
import Button from '@/components/Button.vue';

const { values } = defineProps({ values: Object });
const emit = defineEmits(['update:birthDatePicker']);

const { width } = useWindowSize();

const { getSexo } =ClientUtil.getClientFormat();

const formatMask = width > 639 ? "dd'º de' MMM',' yyyy" : 'dd/MM/yyyy';

const dateToIso = (date) => parseISO(date.toString());

const getDataFormat = (date) => format(dateToIso(date), formatMask, { locale: ptBR });

const value = computed({
  get: () => (values.dataNascimento ? parseDate(values.dataNascimento) : undefined),
  set: (val) => val,
});

const handleBirthDateChange = (value) => emit('update:birthDatePicker', value);
</script>
<template>
  <section class="flex flex-col gap-4 mt-4 space-y-6 sm:grid sm:grid-cols-6 sm:gap-4 sm:space-y-0">
    <FormField v-slot="{ componentField }" name="nome">
      <FormItem v-auto-animate class="sm:col-span-5">
        <FormLabel>Nome</FormLabel>
        <FormControl>
          <Input
            class="focus-visible:ring-slate-500"
            placeholder="Nome completo"
            v-bind="componentField"
            autocomplete="name"
          />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="sexo">
      <FormItem v-auto-animate class="sm:col-span-1">
        <FormLabel> <span class="sm:hidden">selecione seu </span>sexo</FormLabel>
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
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="telefone">
      <FormItem v-auto-animate class="sm:col-span-3">
        <FormLabel>Telefone</FormLabel>
        <FormControl>
          <Input
            v-mask="['(##) ####-####', '(##) #####-####']"
            class="focus-visible:ring-slate-500"
            type="tel"
            placeholder="Número de telefone"
            v-bind="componentField"
            autocomplete="username"
          />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="tipoPessoa">
      <FormItem v-auto-animate class="sm:col-span-3">
        <FormLabel>CPF/CNPJ</FormLabel>
        <FormControl>
          <Input
            v-mask="['###.###.###-##', '##.###.###/####-##']"
            class="focus-visible:ring-slate-500"
            type="text"
            placeholder="Número de telefone"
            v-bind="componentField"
          />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="email">
      <FormItem v-auto-animate class="sm:col-span-4">
        <FormLabel>E-mail</FormLabel>
        <FormControl>
          <Input
            class="focus-visible:ring-slate-500"
            type="email"
            placeholder="E-mail válido"
            v-bind="componentField"
            autocomplete="username"
          />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField name="dataNascimento">
      <FormItem class="flex flex-col sm:col-span-2">
        <FormLabel>Data de Nascimento</FormLabel>
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
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="senha">
      <FormItem v-auto-animate class="sm:col-span-3">
        <FormLabel>Senha</FormLabel>
        <FormControl>
          <Input
            type="password"
            placeholder="Senha"
            v-bind="componentField"
            autocomplete="new-password"
          />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="confirmSenha">
      <FormItem v-auto-animate class="sm:col-span-3">
        <FormLabel>Confirmação de senha</FormLabel>
        <FormControl>
          <Input
            type="password"
            placeholder="Confirme sua senha"
            v-bind="componentField"
            autocomplete="new-password"
          />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
  </section>
</template>
