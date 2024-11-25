<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { SideBar } from '@/components/sideBar';
import { Toggle } from '@/components/ui/toggle'

const page = usePage()
// const isAuth = computed(() => page.props.auth.user)

</script>

<template>

    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 relative" id="header_bar">
                <SideBar></SideBar>
                <h2 class="font-semibold text-sm dark:text-gray-200 leading-tight text-info">
                    <i class="ri-home-2-fill"></i>
                </h2>

                <Toggle aria-label="Toggle sound" id='toggleSound'
                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 group">
                    <i class="ri-volume-mute-fill group-aria-pressed:hidden text-info/50 "></i>
                    <i class="ri-volume-up-fill hidden group-aria-pressed:block text-info/50 "></i>
                </Toggle>

            </div>
        </template>

        <div class="py-12 ">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                    <RouterView />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
