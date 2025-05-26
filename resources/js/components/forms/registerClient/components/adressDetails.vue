<script setup>
import { FormMessage, FormItem, FormField, FormControl, FormLabel } from '@/components/ui/form';
import Button from '@/components/Button.vue';
import { Input } from '@/components/ui/input';
import { RiMapPinLine as MapPinIcon } from 'vue-remix-icons';
import { cn } from '@/lib/utils';
import { useGoogleMapsAddress } from '@/composables/useGoogleMapsAddress';

const emit = defineEmits(['update:addressValue']);

// Função callback para emitir o valor do endereço atualizado
const updateAddressValue = (value) => {
  emit('update:addressValue', value);
};

// Inicializa o composable passando a callback
const { handleLocatorButton } = useGoogleMapsAddress(updateAddressValue);
</script>

<template>
  <section class="flex flex-col gap-4 mt-4 space-y-6 sm:grid sm:grid-cols-12 sm:gap-4 sm:space-y-1">
    <FormField v-slot="{ componentField }" name="search">
      <FormItem class="relative sm:col-span-12">
        <FormControl>
          <Input
            v-bind="componentField"
            id="autocomplete"
            placeholder="procure por um endereço"
            label="Endereço"
            class="!pr-14 !pl-3"
          />
        </FormControl>
        <FormLabel> pesquise </FormLabel>
        <FormMessage />
        <Button
          variant="outline"
          class="rounded-none absolute icon text-xl hover:bg-transparent text-[#45BAE7] hover:text-[#245295] transition-colors top-1/2 -translate-y-1/2 border-l-2 py-1 px-4 border-t-0 border-b-0 border-r-0 right-0 bg-transparent outline-none ring-transparent !m-0"
          @click="(event) => handleLocatorButton(event, updateAddressValue)"
        >
          <MapPinIcon />
        </Button>
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="cep">
      <FormItem class="relative sm:col-span-3">
        <FormControl>
          <Input v-mask="['#####-###']" v-bind="componentField" type="text" />
        </FormControl>
        <FormLabel> CEP </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="cidade">
      <FormItem v-auto-animate class="relative sm:col-span-5">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Cidade </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="estado">
      <FormItem class="relative sm:col-span-2">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Estado </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="apelido">
      <FormItem class="relative sm:col-span-2">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Apelido </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="logradouro">
      <FormItem class="relative sm:col-span-6">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Logradouro </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="numero">
      <FormItem class="relative sm:col-span-2">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Nº </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="bairro">
      <FormItem class="relative sm:col-span-4">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Bairro </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="complemento">
      <FormItem class="relative sm:col-span-6">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Complemento </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="referencia">
      <FormItem class="relative sm:col-span-6">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Referência </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="observacao">
      <FormItem class="relative sm:col-span-12">
        <FormControl>
          <Input v-bind="componentField" autocomplete="name" type="text" />
        </FormControl>
        <FormLabel> Observação </FormLabel>
        <FormMessage />
      </FormItem>
    </FormField>
  </section>
</template>
