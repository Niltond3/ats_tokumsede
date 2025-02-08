<script setup>
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { updateStock } from '@/services/api/stock';
import renderToast from '@/components/renderPromiseToast';

const props = defineProps({
  stockData: { type: Object, required: true },
  loadTable: { type: Function, required: true },
  isNestedTable: { type: Boolean, default: false },
});

const currentStock = ref(props.stockData.quantidade);
const quantity = ref(0);

const handleUpdateStock = async () => {

  const promise = updateStock(props.stockData.id, quantity.value);

  renderToast(
    promise,
    'Atualizando estoque...',
    'Estoque atualizado com sucesso!',
    'Erro ao atualizar estoque',
    () => props.loadTable(),
  );
};
</script>

<template>
  <div class="flex items-center gap-2">
    <span class="text-info font-semibold w-14">{{ currentStock }} + </span>
    <input v-model="quantity" type="number" class="w-20 p-1 border rounded" />
    <Button
      variant="ghost"
      size="sm"
      class="text-info hover:text-info/80"
      @click="handleUpdateStock"
    >
      <i class="ri-save-line" />
    </Button>
  </div>
</template>
