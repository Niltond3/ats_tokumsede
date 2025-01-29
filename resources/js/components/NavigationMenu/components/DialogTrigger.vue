<script setup>
import { DialogTrigger } from '@/components/ui/dialog';

defineProps({
  title: String,
  icon: String,
});
</script>
<template>
  <DialogTrigger as-child>
    <button class="text-info group">
      <i class="text-3xl" :class="icon"></i>
      <span
        class="hidden min-[426px]:block font-semibold text-sm group-hover:scale-125 transition-all"
        >{{ title }}</span
      >
    </button>
  </DialogTrigger>
</template>
