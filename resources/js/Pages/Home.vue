<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
// const isAuth = computed(() => page.props.auth.user)
</script>

<template>
  <div>
    <Head title="Gestão" />

    <AuthenticatedLayout>
      <template #header></template>

      <RouterView />
    </AuthenticatedLayout>
  </div>
</template>
