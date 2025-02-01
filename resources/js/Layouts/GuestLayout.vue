<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/sonner';

const isLoaded = ref(false);

onMounted(() => {
  isLoaded.value = true;
});
</script>

<template>
  <div class="min-h-screen relative overflow-hidden">
    <!-- Enhanced background with gradient and animation -->
    <div
      class="absolute inset-0 bg-gradient-to-b from-background to-muted/50 dark:from-background dark:to-background"
      :class="{ 'opacity-100': isLoaded }"
    >
      <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:60px_60px]" />
    </div>

    <!-- Animated background elements -->
    <div class="absolute inset-0 flex items-center justify-center">
      <div class="bg-water-desktop opacity-10 dark:opacity-5 animate-float" />
    </div>

    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center p-4">
      <!-- Enhanced logo container -->
      <Link href="/" class="mb-8 transform hover:scale-105 transition-transform duration-200">
        <ApplicationLogo class="w-36 h-36" />
      </Link>

      <!-- Enhanced content container -->
      <div class="w-full max-w-md animate-in slide-in-from-bottom-4 duration-700">
        <div class="backdrop-blur-sm bg-background/95 rounded-lg shadow-lg border border-border">
          <slot />
        </div>
      </div>
    </div>

    <Toaster richColors position="top-center" />
  </div>
</template>

<style>
.animate-float {
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}
</style>
