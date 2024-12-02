<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3';
import { getClientFormat } from "@/Pages/Management/utils";
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { CheckIcon, CircleIcon, DotIcon } from '@radix-icons/vue'
import { useForm } from 'vee-validate'
import validator from 'validator'
import { toTypedSchema } from '@vee-validate/zod'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import * as z from 'zod'
import { Form } from '@/components/ui/form'
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import Button from '@/components/Button.vue';
import axios from 'axios';
import PersonalDetails from '../components/personalDetails.vue';
import AdressDetails from '../components/adressDetails.vue';
import ConfirmClient from '../components/confirmClient.vue';
import { FormRegisterClient } from '@/components/forms/registerClient';



</script>

<template>
    <GuestLayout>

        <Head title="Registre-se" />
        <FormRegisterClient />
    </GuestLayout>
</template>
