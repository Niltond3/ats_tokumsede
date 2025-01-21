<script setup>
import {
  ComboboxAnchor,
  ComboboxContent,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxLabel,
  ComboboxRoot,
  ComboboxTrigger,
  ComboboxViewport,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText,
  TagsInputRoot,
} from 'radix-vue';
import { Skeleton } from '@/components/ui/skeleton';

defineProps({
  modelValue: Array,
  searchTerm: String,
  distributors: Array,
  isLoading: Boolean,
  disabled: Boolean,
});

defineEmits(['update:modelValue', 'update:searchTerm']);
</script>

<template>
  <ComboboxRoot
    :model-value="modelValue"
    :search-term="searchTerm"
    multiple
    class="relative"
    :disabled="disabled"
    @update:model-value="$emit('update:modelValue', $event)"
    @update:search-term="$emit('update:searchTerm', $event)"
  >
    <Skeleton v-if="isLoading" class="w-full h-10 rounded-lg" />
    <ComboboxAnchor
      v-else
      class="w-full inline-flex items-center justify-between p-2 text-[13px] leading-none gap-[5px] bg-white shadow-sm border hover:bg-info/10 rounded-sm border-[#ddd] hover:border-[#aaaeb7] transition-all minh-14"
    >
      <TagsInputRoot
        v-slot="{ modelValue: tags }"
        :model-value="modelValue"
        delimiter=""
        class="flex gap-2 items-center rounded-lg flex-wrap"
      >
        <ComboboxInput as-child>
          <TagsInputInput
            placeholder="Distribuidores..."
            class="focus-visible:ring-info/60 block min-h-[auto] w-full rounded bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600 border-input"
            @keydown.enter.prevent
          />
        </ComboboxInput>
        <TagsInputItem
          v-for="item in tags"
          :key="item"
          :value="item.split(' ').slice(1).join(' ')"
          class="flex items-center gap-2 bg-info text-primary-foreground font-semibold rounded px-2 py-1"
        >
          <TagsInputItemText class="text-sm" />
          <TagsInputItemDelete
            @click="
              $emit(
                'update:modelValue',
                modelValue.filter((d) => d !== item),
              )
            "
          >
            <i class="ri-close-line" />
          </TagsInputItemDelete>
        </TagsInputItem>
      </TagsInputRoot>

      <ComboboxTrigger>
        <i class="ri-arrow-down-s-line h-4 w-4" />
      </ComboboxTrigger>
    </ComboboxAnchor>

    <ComboboxContent class="absolute z-10 w-full mt-2 bg-white rounded-md shadow-lg">
      <ComboboxViewport class="p-1">
        <ComboboxEmpty class="text-gray-400 text-sm p-2" />
        <ComboboxGroup>
          <ComboboxLabel class="px-2 text-xs text-gray-500">Distribuidores</ComboboxLabel>
          <ComboboxItem
            v-for="distributor in distributors"
            :key="distributor.id"
            :value="distributor.nome"
            class="relative flex items-center px-2 py-1.5 text-sm rounded-sm hover:bg-gray-100 cursor-pointer text-info font-semibold"
          >
            <ComboboxItemIndicator class="absolute left-1">
              <i class="ri-check-line" />
            </ComboboxItemIndicator>
            <span class="pl-4">{{ distributor.nome }}</span>
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxViewport>
    </ComboboxContent>
  </ComboboxRoot>
</template>
