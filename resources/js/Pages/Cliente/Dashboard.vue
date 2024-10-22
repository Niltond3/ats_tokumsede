<script setup>
import AuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import HomeComponent from '../HomeComponent.vue';
import Overview from '../overview.vue';

/**
<div class="p-6 text-gray-900 dark:text-gray-100">You're logged in!</div>

<HomeComponent />
 */
</script>

<template>

    <Head title="Cliente Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                    <Overview></Overview>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
