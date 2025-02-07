<!-- DialogWrapper.vue -->
<template>
  <!-- O MenuRoot é o provedor necessário para os componentes que usam shadcnvue -->
  <MenuRoot>
    <!-- Renderiza o componente de dialog dinâmico -->
    <component :is="dialogComponent" v-bind="routeProps" />
  </MenuRoot>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
// Importe o MenuRoot do shadcnvue (ajuste o caminho conforme sua configuração)
import { MenuRoot } from 'shadcn-vue'; // ou o caminho correto para seu projeto

// Obtenha os parâmetros da rota
const route = useRoute();
const { dialogName, dialogId } = route.params;

// Reúna as props que serão passadas para o componente dinâmico
// Você pode adicionar outras props conforme necessário
const routeProps = { dialogName, dialogId };

// Utilize o import.meta.glob para mapear todos os componentes de dialog que seguem um padrão
const dialogs = import.meta.glob('@/components/dialogs/Dialog*.vue');

// Computa o componente correto com base no nome do dialog
const dialogComponent = computed(() => {
  // Constrói o caminho esperado (exemplo: "@/components/dialogs/DialogCreateOrder.vue")
  const componentPath = `@/components/dialogs/Dialog${dialogName}.vue`;
  // Retorna o componente se existir; caso contrário, retorna um componente padrão
  return dialogs[componentPath]
    ? dialogs[componentPath]
    : () => import('@/components/dialogs/DefaultDialog.vue');
});
</script>
