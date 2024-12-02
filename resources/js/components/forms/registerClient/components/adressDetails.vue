<script setup>
import { onMounted } from 'vue';
import { FormMessage, FormItem, FormField, FormControl, FormLabel } from '@/components/ui/form'
import Button from '@/components/Button.vue'
import { Input } from '@/components/ui/input'
import { RiMapPinLine as MapPinIcon } from 'vue-remix-icons'
import { cn } from "@/lib/utils";


const emit = defineEmits(['update:addressValue'])

const getTypes = {
    "street_number": (object, value) => {
        return { ...object, numero: value }
    }, // 123
    "route": (object, value) => {
        return { ...object, logradouro: value }
    }, // "Avenida Valdemar Naziazeno" | "Av. Valdemar Naziazeno"
    "sublocality_level_1": (object, value) => {
        return { ...object, bairro: value }
    }, // "Ernesto Geisel"
    "administrative_area_level_2": (object, value) => {
        return { ...object, cidade: value }
    }, // "João Pessoa"
    "administrative_area_level_1": (object, value) => {
        return { ...object, estado: value }
    }, // "Paraíba" | "PB"
    "postal_code": (object, value) => {
        return { ...object, cep: value }
    }, // "58075-000"
    "country": (object, value) => {
        return { ...object, pais: value }
    }, // "Brazil"
}

const handleChange = (value) => emit('update:addressValue', value)

const setPlace = (place) => {
    const { address_components, formatted_address } = place

    const addressComp = address_components.reduce((prev, addressItem) => {
        const { short_name, types } = addressItem


        const type = types[0]

        const typeGetted = getTypes[type]

        if (!!typeGetted) return typeGetted(prev, short_name)
        return prev

    }, {});
    handleChange({ ...addressComp, search: formatted_address })
}

const getUserLocation = () => {
    // Check if geolocation is supported by the browser
    const isSupported = 'navigator' in window && 'geolocation' in navigator
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
                    if (response.results[0]) setPlace(response.results[0])
                    else window.alert("No results found");
                })
                .catch((e) => window.alert("Geocoder failed due to: " + e));
        })
    }
}


const handleLocatorButton = (event) => {
    event.preventDefault();
    getUserLocation()
}

onMounted(() => {
    const options = {
        componentRestrictions: {
            country: 'br'
        },
        strictBounds: true
    };

    const autocomplete = new google.maps.places.Autocomplete(document.getElementById('autocomplete'), options);

    autocomplete.setFields(['place_id', 'geometry', 'address_component', 'formatted_address']);

    const infowindow = new google.maps.InfoWindow();

    autocomplete.addListener('place_changed', function () {
        infowindow.close();

        setPlace(autocomplete.getPlace())

    });

    setTimeout(() => (document.body.style.pointerEvents = ""), 0)
})

</script>

<template>
    <section class="flex flex-col gap-4 mt-4 space-y-6 sm:grid sm:grid-cols-12 sm:gap-4 sm:space-y-0">
        <FormField v-slot="{ componentField }" name="search">
            <FormItem class="relative sm:col-span-12 ">
                <FormControl>
                    <Input v-bind="componentField" placeholder="procure por um endereço" label="Endereço"
                        id="autocomplete" :class="cn(inputClass, '!pr-14 !pl-3')" />
                </FormControl>
                <FormLabel>
                    pesquise
                </FormLabel>
                <FormMessage />
                <Button variant="outline" @click="handleLocatorButton"
                    class="rounded-none absolute icon text-xl hover:bg-transparent text-[#45BAE7] hover:text-[#245295] transition-colors top-1/2 -translate-y-1/2 border-l-2 py-1 px-4 border-t-0 border-b-0 border-r-0 right-0 bg-transparent outline-none ring-transparent !m-0">
                    <MapPinIcon />
                </Button>
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="cep">
            <FormItem class="relative sm:col-span-3">
                <FormControl>
                    <Input v-bind="componentField" type="text" v-mask="['#####-###']" />
                </FormControl>
                <FormLabel>
                    CEP
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="cidade">
            <FormItem v-auto-animate class="relative sm:col-span-5">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Cidade
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="estado">
            <FormItem class="relative sm:col-span-2">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Estado
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="apelido">
            <FormItem class="relative sm:col-span-2">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Apelido
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="logradouro">
            <FormItem class="relative sm:col-span-6">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Logradouro
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="numero">
            <FormItem class="relative sm:col-span-2">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Nº
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="bairro">
            <FormItem class="relative sm:col-span-4">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Bairro
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="complemento">
            <FormItem class="relative sm:col-span-6">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Complemento
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="referencia">
            <FormItem class="relative sm:col-span-6">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Referência
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="observacao">
            <FormItem class="relative sm:col-span-12">
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" type="text" />
                </FormControl>
                <FormLabel>
                    Observação
                </FormLabel>
                <FormMessage />
            </FormItem>
        </FormField>
    </section>
</template>
