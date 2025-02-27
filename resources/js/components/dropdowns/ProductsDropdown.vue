<script setup>
import { ref, watch } from 'vue';
import { defineProps, defineEmits } from 'vue';
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
  ComboboxTrigger,
} from '@/components/ui/combobox';

import { Check, ChevronsUpDown, Search } from 'lucide-vue-next';

// Recebe via prop a lista de produtos
const props = defineProps({
  products: {
    type: Array,
    default: () => [],
  },
});

// Emite o evento quando um produto é selecionado
const emits = defineEmits(['product-selected']);

// Valor selecionado (objeto do produto)
const selectedProduct = ref(null);

// Sempre que o produto selecionado mudar, emite o evento com os dados
watch(selectedProduct, (newVal) => {
  console.log(newVal);
  if (newVal) {
    emits('product-selected', newVal.id);
  }
});
</script>

<template>
  <Combobox v-model="selectedProduct" by="nome">
    <ComboboxAnchor>
      <div class="relative w-full max-w-sm">
        <ComboboxInput
          class="pr-9 focus-visible:ring-0 border-0 border-b rounded-none h-10"
          :display-value="(val) => val?.nome ?? ''"
          placeholder="Atualize um produto"
        />
        <ComboboxTrigger class="absolute end-0 inset-y-0 flex items-center justify-center px-3">
          <ChevronsUpDown class="size-4 text-muted-foreground" />
        </ComboboxTrigger>
      </div>
    </ComboboxAnchor>
    <ComboboxList class="max-h-52 border-info/20">
      <ComboboxEmpty>Nenhum produto encontrado.</ComboboxEmpty>
      <ComboboxGroup>
        <ComboboxItem v-for="product in props.products" :key="product.id" :value="product">
          {{ product.nome }}
          <ComboboxItemIndicator>
            <Check class="ml-auto h-4 w-4" />
          </ComboboxItemIndicator>
        </ComboboxItem>
      </ComboboxGroup>
    </ComboboxList>
  </Combobox>
</template>
