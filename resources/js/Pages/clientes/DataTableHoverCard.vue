<script setup>
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    HoverCard,
    HoverCardContent,
    HoverCardTrigger,
} from '@/components/ui/hover-card'
import { MapIcon } from 'lucide-vue-next'

const props = defineProps({
    payment: {
        enderecos: Array
    }
})

function copy(id) {
    navigator.clipboard.writeText(id)
}

</script>
<template>
    <HoverCard :open-delay="100">
        <HoverCardTrigger as-child>
            <Button variant="ghost" class="transition-colors text-cyan-700 p-0">
                <span class="sr-only">Open menu</span>
                <MapIcon class="w-6 h-6" />
            </Button>
        </HoverCardTrigger>
        <HoverCardContent align="end">
            {{ props.payment }}
        </HoverCardContent>
    </HoverCard>
</template>
