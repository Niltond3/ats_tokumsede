<script setup>
import { Button } from '@/components/ui/button';
const props = defineProps({
  href: { type: String, required: false },
});
</script>
<!-- btn--primary -->
<!-- dashboard -->

<template>
  <Button v-if="href !== undefined" class="" as-child variant="ghost">
    <a :href="href">
      <slot />
    </a>
  </Button>

  <Button v-else class="">
    <slot />
  </Button>
</template>
