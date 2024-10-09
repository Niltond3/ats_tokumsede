<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { useGeolocation } from '@/useGeolocation'
import { Loader } from '@googlemaps/js-api-loader'
import { Input } from '@/components/ui/input'
import { FormLabel, FormControl, FormMessage, FormItem, FormField } from '@/components/ui/form'

const GOOGLE_MAPS_API_KEY = 'AIzaSyD3A65oIloNfr-TA3EK8vERo2nnWEi1fxg'


</script>
<template v-if="stepIndex === 2">
    <section>


        <FormField v-slot="{ componentField }" name="password">
            <FormItem>
                <FormLabel>Password</FormLabel>
                <FormControl>
                    <Input type="password" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="confirmPassword">
            <FormItem>
                <FormLabel>Confirm Password</FormLabel>
                <FormControl>
                    <Input type="password" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>
    </section>
</template>
