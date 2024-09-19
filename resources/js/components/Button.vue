<script setup>
import { Button } from '@/components/ui/button'

const props = defineProps({
    'href': { type: String, required: false },
})
</script>
<!-- btn--primary -->

<template>
    <Button v-if="href !== null" class="" as-child="True" variant="ghost">
        <a :href="href" >
            <slot />
            <!-- <i class="icon icon--chevron-right text-base" v-if="Button.classList.contains('btn--primary')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 20">
                    <fill="none" stroke="currentColor" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 19 9-9-9-9"/>
                </svg>
            </i> -->
        </a>
    </Button>

    <Button v-else class="">
        <slot />
    </Button>
</template>

