<script setup>
import { watch } from 'vue';
import { CommandEmpty, CommandGroup, CommandItem, CommandList } from '@/components/ui/command';
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText,
} from '@/components/ui/tags-input';
import {
  ComboboxAnchor,
  ComboboxContent,
  ComboboxInput,
  ComboboxPortal,
  ComboboxRoot,
} from 'radix-vue';
import { StringUtil } from '@/util';
import { listProducts } from '@/services/api/products';
import { useProductSelector } from '@/composables/useProductSelector';

const emits = defineEmits(['update:modelValue']);

const props = defineProps({
  initialProducts: {
    type: Array,
    default: () => [],
  },
});

const {
  addProduct,
  removeProduct,
  searchTerm,
  open,
  filteredProducts,
  selectedProducts,
  setSelectedProducts,
} = useProductSelector();

// Delay para evitar que o combobox feche antes do clique ser processado
const handleInputBlur = () => {
  setTimeout(() => {
    open.value = false;
  }, 150);
};

// Atualiza o searchTerm conforme o usuário digita
const updateSearchTerm = (e) => {
  searchTerm.value = e.target.value;
};

// Watch for initial products and set them
watch(
  () => props.initialProducts,
  (newProducts) => {
    setSelectedProducts(newProducts);
  },
  { immediate: true },
);

const handleSelect = (product) => {
  console.log('Produto selecionado:', product);
  addProduct(product);
  emits(
    'update:modelValue',
    selectedProducts.value.map((item) => `${item.id}-1`),
  );
  searchTerm.value = '';
};

const handleRemoveProduct = (id) => {
  removeProduct(id);
  emits(
    'update:modelValue',
    selectedProducts.value.map((item) => `${item.id}-1`),
  );
};

const handleSelectionChange = (selectedIds) => {
  emits(
    'update:modelValue',
    selectedIds.map((id) => `${id}-1`),
  );
};
</script>

<template>
  <TagsInput class="py-0 px-0 gap-0" :model-value="selectedProducts">
    <!-- Renderização das tags dos produtos selecionados -->
    <div class="flex gap-2 flex-wrap items-center px-3">
      <TagsInputItem v-for="item in selectedProducts" :key="item.id" :value="item.nome">
        <TagsInputItemText />
        <!-- Utilize @click.stop para garantir que o clique não afete outros elementos -->
        <TagsInputItemDelete @click.stop="() => handleRemoveProduct(item.id)" />
      </TagsInputItem>
    </div>

    <!-- Combobox para selecionar produtos -->
    <ComboboxRoot :open="open" :search-term="searchTerm" class="w-full">
      <ComboboxAnchor as-child>
        <ComboboxInput placeholder="Produtos da composição..." as-child>
          <TagsInputInput
            class="w-full px-3"
            :class="selectedProducts.length > 0 ? 'mt-2' : ''"
            @keydown.enter.prevent
            @focus="open = true"
            @blur="handleInputBlur"
            @input="updateSearchTerm"
          />
        </ComboboxInput>
      </ComboboxAnchor>

      <ComboboxPortal>
        <ComboboxContent>
          <CommandList
            position="popper"
            class="w-[--radix-popper-anchor-width] rounded-md mt-2 border border-input bg-popover text-popover-foreground shadow-md outline-none"
          >
            <CommandEmpty />
            <CommandGroup>
              <div
                v-if="filteredProducts.length === 0"
                class="px-2 py-1 text-sm text-muted-foreground"
              >
                Nenhum produto encontrado.
              </div>
              <div v-else>
                <CommandItem
                  v-for="product in filteredProducts"
                  :key="product.id"
                  :value="product.nome"
                  @mousedown.prevent="() => handleSelect(product)"
                >
                  {{ product.nome }}
                </CommandItem>
              </div>
            </CommandGroup>
          </CommandList>
        </ComboboxContent>
      </ComboboxPortal>
    </ComboboxRoot>
  </TagsInput>
</template>
