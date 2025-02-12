<!-- components/navigation/NavigationMenuItem.vue -->
<script setup>
import {
  NavigationMenuItem,
  NavigationMenuTrigger,
  NavigationMenuContent,
} from '@/components/ui/navigation-menu';

defineProps({
  label: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <NavigationMenuItem>
    <NavigationMenuTrigger
      class="text-info/80 hover:text-info transition-colors font-semibold focus:text-accepted"
    >
      {{ label }}
    </NavigationMenuTrigger>
    <NavigationMenuContent>
      <slot />
    </NavigationMenuContent>
  </NavigationMenuItem>
</template>
