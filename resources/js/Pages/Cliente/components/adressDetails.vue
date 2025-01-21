<script setup>
import { RiMapPinLine as MapPinIcon } from 'vue-remix-icons';
import { Input } from '@/components/ui/input';
import { FormLabel, FormControl, FormMessage, FormItem, FormField } from '@/components/ui/form';
import Button from '@/components/Button.vue';

const emit = defineEmits(['update:addressValue']);

const getTypes = {
  street_number: (object, value) => {
    return { ...object, numero: value };
  }, // 123
  route: (object, value) => {
    return { ...object, logradouro: value };
  }, // "Avenida Valdemar Naziazeno" | "Av. Valdemar Naziazeno"
  sublocality_level_1: (object, value) => {
    return { ...object, bairro: value };
  }, // "Ernesto Geisel"
  administrative_area_level_2: (object, value) => {
    return { ...object, cidade: value };
  }, // "João Pessoa"
  administrative_area_level_1: (object, value) => {
    return { ...object, estado: value };
  }, // "Paraíba" | "PB"
  postal_code: (object, value) => {
    return { ...object, cep: value };
  }, // "58075-000"
  country: (object, value) => {
    return { ...object, pais: value };
  }, // "Brazil"
};

const handleChange = (value) => {
  emit('update:addressValue', value);
};
const setPlace = (place) => {
  const { address_components, formatted_address } = place;
  const addressComp = address_components.reduce((prev, addressItem) => {
    const { short_name, types } = addressItem;

    const type = types[0];

    const typeGetted = getTypes[type];

    if (!!typeGetted) return typeGetted(prev, short_name);
    return prev;
  }, {});
  handleChange({ ...addressComp, search: formatted_address });
};

const getUserLocation = () => {
  // Check if geolocation is supported by the browser
  const isSupported = 'navigator' in window && 'geolocation' in navigator;
  if (isSupported) {
    // Retrieve the user's current position
    navigator.geolocation.getCurrentPosition((position) => {
      const latlng = {
        lat: parseFloat(position.coords.latitude),
        lng: parseFloat(position.coords.longitude),
      };

      const geocoder = new google.maps.Geocoder();

      geocoder
        .geocode({ location: latlng })
        .then((response) => {
          if (response.results[0]) setPlace(response.results[0]);
          else window.alert('No results found');
        })
        .catch((e) => window.alert('Geocoder failed due to: ' + e));
    });
  }
};

const handleLocatorButton = (event) => {
  event.preventDefault();
  getUserLocation();
};
</script>

<template>
  <section class="flex flex-col gap-4 mt-4 space-y-6 sm:grid sm:grid-cols-12 sm:gap-4 sm:space-y-0">
    <FormField v-slot="{ componentField }" name="search">
      <FormItem class="sm:col-span-12 relative">
        <FormLabel>Pesquisar endereço</FormLabel>
        <FormControl>
          <!-- <Input class="pr-14" v-bind="componentField" /> -->
          <GMapAutocomplete
            v-bind="componentField"
            class="pr-14 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            placeholder="Search for a location"
            @place_changed="setPlace"
          >
          </GMapAutocomplete>
          <Button
            variant="outline"
            class="rounded-none absolute icon text-xl hover:bg-transparent text-[#45BAE7] hover:text-[#245295] transition-colors top-1/2 -translate-y-1/2 border-l-2 py-1 px-4 border-t-0 border-b-0 border-r-0 !mt-4 right-0 bg-transparent outline-none ring-transparent"
            @click="handleLocatorButton"
          >
            <MapPinIcon />
          </Button>
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="cep">
      <FormItem class="sm:col-span-3">
        <FormLabel>CEP</FormLabel>
        <FormControl>
          <Input v-mask="['#####-###']" v-bind="componentField" type="text" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="cidade">
      <FormItem v-auto-animate class="sm:col-span-5">
        <FormLabel>Cidade</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="estado">
      <FormItem class="sm:col-span-2">
        <FormLabel>Estado</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="apelido">
      <FormItem class="sm:col-span-2">
        <FormLabel>Apelido</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="logradouro">
      <FormItem class="sm:col-span-6">
        <FormLabel>Logradouro</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="numero">
      <FormItem class="sm:col-span-2">
        <FormLabel>Nº</FormLabel>
        <FormControl>
          <Input v-mask="['####']" v-bind="componentField" type="text" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="bairro">
      <FormItem class="sm:col-span-4">
        <FormLabel>Bairro</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="complemento">
      <FormItem class="sm:col-span-6">
        <FormLabel>Complemento</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="referencia">
      <FormItem class="sm:col-span-6">
        <FormLabel>Referência</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <FormField v-slot="{ componentField }" name="observacao">
      <FormItem class="sm:col-span-12">
        <FormLabel>Observação</FormLabel>
        <FormControl>
          <Input v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
  </section>
</template>
