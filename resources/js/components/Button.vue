<script setup>
import { Button } from '@/components/ui/button'

const props = defineProps({
    'href': { type: String, required: false },
})
</script>
<!-- btn--primary -->

<template>
    <Button v-if="href !== undefined" class="" as-child="True" variant="ghost">
        <a :href="href">
            <slot />
        </a>
    </Button>

    <Button v-else class="">
        <slot />
    </Button>
</template>
