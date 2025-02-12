<script setup>
defineProps({
  count: {
    type: Number,
    required: true,
  },
});
</script>

<template>
  <div
    class="absolute items-center flex justify-center w-6 h-6 text-xs font-bold text-white bg-danger border-2 border-white rounded-full -top-3 -right-6 -end-2 dark:border-gray-900 animate-pulse m-auto transition-all [transition-behavior:allow-discrete]"
  >
    <span>{{ count }}</span>
  </div>
</template>
