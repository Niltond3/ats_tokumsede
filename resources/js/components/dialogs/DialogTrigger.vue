<script setup>
import { DialogTrigger } from '@/components/ui/dialog';

defineProps({
  title: String,
  icon: String,
});
</script>
<template>
  <DialogTrigger as-child>
    <button
      class="relative group flex items-center justify-start py-1.5 px-2 rounded-md gap-2 bg-info w-full"
    >
      <i class="text-2xl size-9 rounded-full bg-white absolute text-info" :class="icon"></i>
      <span
        class="ml-11 font-semibold text-sm transition-all text-white whitespace-nowrap font-sans"
        >{{ title }}</span
      >
    </button>
  </DialogTrigger>
</template>
