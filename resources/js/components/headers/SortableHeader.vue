<script setup>
import { Button } from '@/components/ui/button';
import { ArrowUpDown } from 'lucide-vue-next';

defineProps({
  label: String,
  onSort: Function,
});
</script>

<template>
  <Button
    class="hover:!bg-transparent ring-transparent p-0 hover:!text-white text-sm font-bold text-white"
    variant="ghost"
    @click="onSort"
  >
    {{ label }}
    <ArrowUpDown
      class="hover:!bg-transparent ring-transparent p-0 hover:!text-white ml-2 h-4 w-4"
    />
  </Button>
</template>
