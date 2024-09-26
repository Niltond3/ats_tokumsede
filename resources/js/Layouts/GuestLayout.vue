<script setup>
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="bg-water-desktop bg-[center_top] w-full h-full fixed bottom-[-30rem]"></div>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 ">

        <div>
            <Link href="/">
            <ApplicationLogo class="w-36 h-36 z-10" />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 backdrop-blur-md bg-white/30 dark:bg-gray-800/30 shadow-md overflow-hidden sm:rounded-lg z-10 ">
            <slot />
        </div>
    </div>
</template>
