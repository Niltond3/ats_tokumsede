<script setup>
import { DonutChart } from '@/components/ui/chart-donut';
import CustomChartTooltip from './CustomChartTooltip.vue';

const props = defineProps({
    data: { type: null, required: true },
    colors: { type: null, required: false },
});

</script>

<template>
    <DonutChart index="name" :category="'total'" :data="props.data" :type="'pie'" :custom-tooltip="CustomChartTooltip"
        :colors="props.colors ?? [
            '#1E88E5',
            '#26c6da',
            '#0e7490'
            ]" />
</template>
