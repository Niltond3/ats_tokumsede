<script setup>
import { ref } from 'vue';
import { Switch } from '@/components/ui/switch';
import { cn } from '@/util';
import { updateProductStatus } from '@/services/api/products';
import renderToast from '@/components/renderPromiseToast';

const props = defineProps({
  stockData: { type: Object, required: true },
  loadTable: { type: Function, required: true },
});

const isActive = ref(props.stockData.produto.status === 1);

const handleUpdateStatus = () => {
  const promise = updateProductStatus(props.stockData.produto.id, isActive.value ? 1 : 3);

  renderToast(
    promise,
    'Atualizando status...',
    'Status atualizado com sucesso!',
    'Erro ao atualizar status',
    props.loadTable,
  );
};
</script>

<template>
  <div class="flex items-center gap-2">
    <Switch
      :checked="isActive"
      class="data-[state=checked]:bg-success data-[state=unchecked]:bg-destructive/40"
      @update:checked="
        (val) => {
          isActive = val;
          handleUpdateStatus();
        }
      "
    />
    <span
      :class="
        cn([
          'text-sm font-medium transition-colors',
          isActive ? 'text-success' : 'text-destructive/40',
        ])
      "
    >
      {{ isActive ? 'Ativo' : 'Inativo' }}
    </span>
  </div>
</template>
