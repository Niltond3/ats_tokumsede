<script setup>
import { computed, ref } from 'vue'
import * as z from 'zod'
import { Form, FormField } from '@/components/ui/form'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { NavigationMenuLink } from '@/components/ui/navigation-menu'
import { dialogState } from '@/hooks/useToggleDialog'
import { toTypedSchema } from '@vee-validate/zod'
import renderToast from '../renderPromiseToast';
import {
    FormItem,
    FormLabel,
    FormControl,
    FormDescription,
    FormMessage
} from '../ui/form';
import { useDropzone } from "vue3-dropzone";
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { onMounted } from 'vue';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { CommandEmpty, CommandGroup, CommandItem, CommandList } from '@/components/ui/command'
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input'
import { ComboboxAnchor, ComboboxContent, ComboboxInput, ComboboxPortal, ComboboxRoot } from 'radix-vue'
import { Label } from '../ui/label';
import { utf8Decode } from '@/util';

const products = ref([])
const modelValue = ([])
const open = ref(false)
const searchTerm = ref('')

const filteredProducts = computed(() => products.value.filter(i => !modelValue.value.includes(i.nome)))

function getFirstLetter(str) {
    // Divide a string em palavras e pega a primeira letra de cada palavra
    const palavras = str.split(' ');
    const iniciais = palavras.map(palavra => palavra.charAt(0).toUpperCase());

    // Junta as iniciais em uma única string
    return iniciais.join('');
}
const getProducts = () => {
    const url = '/produtos'
    const promise = axios.get(url)
    renderToast(promise, 'carregando produtos ...', 'Produtos carregados', (response) => {
        console.log(response.data)
        const recomposeProducts = response.data.map((product) => {
            const nome = utf8Decode(product.nome)
            const label = getFirstLetter(nome)
            return {
                ...product,
                nome,
                label
            }
        })
        products.value = recomposeProducts
    })
}

onMounted(() => {
    getProducts()
})

</script>

<template>
    <TagsInput class="px-0 gap-0 w-80" :model-value="modelValue">
        <div class="flex gap-2 flex-wrap items-center px-3">
            <TagsInputItem v-for="item in modelValue" :key="item" :value="item">
                <TagsInputItemText />
                <TagsInputItemDelete />
            </TagsInputItem>
        </div>

        <ComboboxRoot v-model="modelValue" v-model:open="open" v-model:search-term="searchTerm" class="w-full">
            <ComboboxAnchor as-child>
                <ComboboxInput placeholder="Framework..." as-child>
                    <TagsInputInput class="w-full px-3" :class="modelValue.length > 0 ? 'mt-2' : ''"
                        @keydown.enter.prevent />
                </ComboboxInput>
            </ComboboxAnchor>

            <ComboboxPortal>
                <ComboboxContent>
                    <CommandList position="popper"
                        class="w-[--radix-popper-anchor-width] rounded-md mt-2 border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2">
                        <CommandEmpty />
                        <CommandGroup>
                            <CommandItem v-for="product in filteredProducts" :key="product.id" :value="product.label"
                                @select.prevent="(ev) => {
                                    if (typeof ev.detail.value === 'string') {
                                        searchTerm = ''
                                        modelValue.push(ev.detail.value)
                                    }

                                    if (filteredProducts.length === 0) {
                                        open = false
                                    }
                                }">
                                {{ product.label }}
                            </CommandItem>
                        </CommandGroup>
                    </CommandList>
                </ComboboxContent>
            </ComboboxPortal>
        </ComboboxRoot>
    </TagsInput>
</template>
