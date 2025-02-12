<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import Management from '@/Pages/Management/Management.vue';

const page = usePage();
// const isAuth = computed(() => page.props.auth.user)
const props = defineProps({
  currentTab: {
    type: String,
    default: null,
  },
});
</script>

<template>
  <div>
    <Head title="Gestão" />
    <AuthenticatedLayout>
      <template #header></template>
      <Management :initial-tab="currentTab" />
    </AuthenticatedLayout>
  </div>
</template>
