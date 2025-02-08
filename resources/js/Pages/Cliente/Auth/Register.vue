<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { FormRegisterClient } from '@/components/forms/registerClient';
</script>

<template>
  <GuestLayout>
    <Head title="Registre-se" />
    <FormRegisterClient />
  </GuestLayout>
</template>
