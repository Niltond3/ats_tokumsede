<script setup>
import {
    ComboboxAnchor, ComboboxContent, ComboboxEmpty, ComboboxGroup,
    ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxLabel,
    ComboboxRoot, ComboboxTrigger, ComboboxViewport,
    TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText,
    TagsInputRoot
} from 'radix-vue'
import { Skeleton } from '@/components/ui/skeleton'

defineProps({
    modelValue: Array,
    searchTerm: String,
    distributors: Array,
    isLoading: Boolean
})

defineEmits(['update:modelValue', 'update:searchTerm'])
</script>

<template>
    <ComboboxRoot
        :model-value="modelValue"
        :search-term="searchTerm"
        multiple
        class="relative"
        @update:model-value="$emit('update:modelValue', $event)"
        @update:search-term="$emit('update:searchTerm', $event)"
    >
        <Skeleton v-if="isLoading" class="w-full h-10 rounded-lg" />
        <ComboboxAnchor v-else class="w-full inline-flex items-center justify-between rounded-lg p-2 text-[13px] leading-none gap-[5px] bg-white shadow-sm border hover:bg-gray-50">
            <TagsInputRoot v-slot="{ modelValue: tags }" :model-value="modelValue" delimiter="" class="flex gap-2 items-center rounded-lg flex-wrap">
                <TagsInputItem v-for="item in tags" :key="item" :value="item" class="flex items-center gap-2 bg-primary text-primary-foreground rounded px-2 py-1">
                    <TagsInputItemText class="text-sm" />
                    <TagsInputItemDelete @click="$emit('update:modelValue', modelValue.filter(d => d !== item))">
                        <i class="ri-close-line" />
                    </TagsInputItemDelete>
                </TagsInputItem>

                <ComboboxInput as-child>
                    <TagsInputInput placeholder="Selecione os distribuidores..." class="focus:outline-none flex-1 rounded !bg-transparent" @keydown.enter.prevent />
                </ComboboxInput>
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
                    <ComboboxItem v-for="distributor in distributors" :key="distributor.id" :value="distributor.nome" class="relative flex items-center px-2 py-1.5 text-sm rounded-sm hover:bg-gray-100 cursor-pointer">
                        <ComboboxItemIndicator class="absolute left-1">
                            <i class="ri-check-line" />
                        </ComboboxItemIndicator>
                        <span>{{ distributor.nome }}</span>
                    </ComboboxItem>
                </ComboboxGroup>
            </ComboboxViewport>
        </ComboboxContent>
    </ComboboxRoot>
</template>
