<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { useGeolocation } from '@vueuse/core'
import { Loader } from '@googlemaps/js-api-loader'
import { RiMapPinLine as MapPinIcon } from 'vue-remix-icons'
import { Input } from '@/components/ui/input'
import { FormLabel, FormControl, FormMessage, FormItem, FormField } from '@/components/ui/form'
import Button from '@/components/Button.vue'

const GOOGLE_MAPS_API_KEY = 'AIzaSyD3A65oIloNfr-TA3EK8vERo2nnWEi1fxg'

// Get users' current location
const getUserLocation = () => {
    // Check if geolocation is supported by the browser
    const isSupported = 'navigator' in window && 'geolocation' in navigator
    if (isSupported) {
        // Retrieve the user's current position
        navigator.geolocation.getCurrentPosition((position) => {
            coords.value.lat = position.coords.latitude
            coords.value.lng = position.coords.longitude
        })
    }
}

const setPlace = (place) => {
    const { address_components, formatted_address } = place

    console.log(address_components)
    console.log(formatted_address)
}

const handleLocatorButton = (event) => {
    event.preventDefault();
    console.log(coords.value.latitude)
    console.log(coords.value.longitude)
    console.log(error.value)
    // if (navigator.geolocation) {
    //     navigator.geolocation.getCurrentPosition(showPosition, showPositionError, { timeout: 30000 });
    // } else {
    //     console.log("Geolocation is not supported by this browser.")

    // }

    // function showPosition(position) {
    //     console.log("Latitude: " + position.coords.latitude +
    //         "Longitude: " + position.coords.longitude)
    // }
    // function showPositionError(error) {
    //     console.log(error)
    // }
}

</script>
//
<template>
    <section class="flex flex-col gap-4 mt-4 space-y-6 sm:grid sm:grid-cols-12 sm:gap-4 sm:space-y-0">
        <FormField v-slot="{ componentField }" name="search">
            <FormItem class="sm:col-span-12 relative">
                <FormLabel>Pesquisar endereço</FormLabel>
                <FormControl>
                    <!-- <Input class="pr-14" v-bind="componentField" /> -->
                    <GMapAutocomplete
                        class="pr-14 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Search for a location" @place_changed="setPlace">
                    </GMapAutocomplete>
                    <Button variant="outline" @click="handleLocatorButton"
                        class="rounded-none absolute icon text-xl hover:bg-transparent text-[#45BAE7] hover:text-[#245295] transition-colors top-1/2 -translate-y-1/2 border-l-2 py-1 px-4 border-t-0 border-b-0 border-r-0 !mt-4 right-0 bg-transparent outline-none ring-transparent">
                        <MapPinIcon />
                    </Button>
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>
        <!--
        <GMapMap :center="{ lat: 51.5072, lng: 0.1276 }" :zoom="3" map-type-id="terrain"
            class="sm:col-span-12 w-full h-64" :options="{
                zoomControl: true,
                mapTypeControl: true,
                scaleControl: true,
                streetViewControl: true,
                rotateControl: true,
                fullscreenControl: true
            }" /> -->
        <FormField v-slot="{ componentField }" name="cep">
            <FormItem class="sm:col-span-3">
                <FormLabel>CEP</FormLabel>
                <FormControl>
                    <Input v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="cidade">
            <FormItem class="sm:col-span-5">
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
                    <Input type="number" v-bind="componentField" />
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
