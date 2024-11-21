<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/components/welcome/Header.vue';
import WhatInside from '@/components/welcome/WhatInside.vue';
import AboutUs from '@/components/welcome/AboutUs.vue';
import Informations from '@/components/welcome/Informations.vue';
import Support from '@/components/welcome/Support.vue';
import GetInTouch from '@/components/welcome/GetInTouch.vue';
import Footer from '@/components/welcome/Footer.vue';
import DeliveryService from '@/components/welcome/DeliveryService.vue';


defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('aguaalcalina')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

</script>

<template>

    <Head title="Ats - Águas Terra Santa"></Head>
    <div class="bg-gray-50 text-black/50 dark:bg-[#0B1020] dark:text-white/50 w-full overflow-x-hidden">
        <Header />
        <WhatInside />
        <DeliveryService />
        <AboutUs />
        <Informations />
        <Support />
        <GetInTouch />
        <Footer />
    </div>
</template>
