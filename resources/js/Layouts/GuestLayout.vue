<script setup>
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/sonner'

</script>

<template>
    <Toaster richColors />

    <div class="bg-water-desktop bg-[center_top] w-full h-full fixed bottom-[-30rem]"></div>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 ">

        <div>
            <Link href="/">
            <ApplicationLogo class="w-36 h-36 z-10" />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg z-10 grid gap-4  max-h-96 overflow-y-scroll text-xs scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2 bg-white/30 backdrop-blur-sm">
            <slot />
        </div>
    </div>
</template>
