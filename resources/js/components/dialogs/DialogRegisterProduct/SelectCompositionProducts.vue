<script setup>
import { computed, ref } from 'vue';
import renderToast from '@/components/renderPromiseToast';
import { onMounted } from 'vue';
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

const products = ref([]);
const modelValue = ref([]);
const open = ref(false);
const searchTerm = ref('');

const emits = defineEmits(['update:modelValue']);

const getProducts = () => {
  renderToast(
    listProducts(),
    'carregando produtos ...',
    'Produtos carregados',
    'Erro ao carregar produtos',
    (response) => {
      const recomposeProducts = response.data.map((product) => {
        const nome = StringUtil.utf8Decode(product.nome);
        return {
          ...product,
          nome,
        };
      });
      products.value = recomposeProducts;
    },
  );
};

onMounted(() => {
  getProducts();
});

const filteredProducts = computed(() =>
  products.value.filter((i) => !modelValue.value.includes(i.nome)),
);

const handleModelValue = (value) => {
  const compositionValue = value.map((item) => `${item.id}-1`);
  emits('update:modelValue', compositionValue);
};
</script>

<template>
  <TagsInput
    class="py-0 px-0 gap-0"
    :model-value="modelValue"
    @update:model-value="handleModelValue"
  >
    <div class="flex gap-2 flex-wrap items-center px-3">
      <TagsInputItem v-for="item in modelValue" :key="item.id" :value="item.nome">
        <TagsInputItemText />
        <TagsInputItemDelete />
      </TagsInputItem>
    </div>

    <ComboboxRoot v-model="modelValue" :open="open" :search-term="searchTerm" class="w-full">
      <ComboboxAnchor as-child>
        <ComboboxInput placeholder="Produtos da composição..." as-child>
          <TagsInputInput
            class="w-full px-3"
            :class="modelValue.length > 0 ? 'mt-2' : ''"
            @keydown.enter.prevent
          />
        </ComboboxInput>
      </ComboboxAnchor>

      <ComboboxPortal>
        <ComboboxContent>
          <CommandList
            position="popper"
            class="w-[--radix-popper-anchor-width] rounded-md mt-2 border border-input bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2"
          >
            <CommandEmpty />
            <CommandGroup>
              <div
                v-if="filteredProducts.length === 0"
                class="px-2 py-1 text-sm text-muted-foreground"
              ></div>
              <div v-else>
                <CommandItem
                  v-for="product in filteredProducts"
                  :key="product.id"
                  :value="product.nome"
                  @select.prevent="
                    (ev) => {
                      if (typeof ev.detail.value === 'string') {
                        searchTerm = '';
                        const modelObject = {
                          id: product.id,
                          nome: ev.detail.value,
                        };
                        modelValue.push(modelObject);
                      }

                      if (filteredProducts.length === 0) {
                        open = false;
                      }
                    }
                  "
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
