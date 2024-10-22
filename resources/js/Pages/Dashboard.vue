<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import HomeComponent from './HomeComponent.vue';
import { SideBar } from '@/components/sideBar';

const page = usePage()
// const isAuth = computed(() => page.props.auth.user)
console.log(page.props.auth)
console.log('dashboard')


</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <SideBar></SideBar>
                <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
            </div>
        </template>

        <div class="py-12 ">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                    <HomeComponent />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
