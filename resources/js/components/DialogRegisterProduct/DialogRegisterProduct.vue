<script setup>
import { ref } from 'vue';
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
import { Label } from '../ui/label';
import { utf8Decode } from '@/util';
import SelectCompositionProducts from './SelectCompositionProducts.vue';

const categories = ref([])
const isLoading = ref(true)
const productDetails = ref()

const { isOpen, toggleDialog } = dialogState()

const { getRootProps, getInputProps, ...rest } = useDropzone({ onDrop });

const disabledButton = ref(false)

const formSchema = z.object({
    idCategoria: z.string().nullable().optional(),
    nome: z.string().nullable().optional(),
    descricao: z.string().nullable().optional(),
    img: z.string().nullable().optional(),
    itensComposicao: z.array(z.string()).nullable().optional(),
})

const props = defineProps({
    triggerIcon: { type: String, required: true },
    triggerLabel: { type: String, required: true },
})

const emits = defineEmits(["on:create", 'update:dialogOpen']);

const handleCreate = () => {
    console.log('create product')
}

const handleDialogOpen = (op) => {
    !op && emits('update:dialogOpen', false)
    toggleDialog()
}

function onDrop(acceptFiles, rejectReasons) {
    console.log(acceptFiles);
    console.log(rejectReasons);
    //uploadImage(acceptFiles)
}

const uploadImage = async (image) => {
    //if (!image.value) {
    //    errorMessage.value = "Por favor, selecione uma imagem.";
    //    return;
    //}

    const formData = new FormData();
    formData.append('image', image[0]);

    try {
        const response = await axios.post('/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // Exibe a imagem carregada
        console.log(response.data.path)
        //errorMessage.value = ''; // Limpar mensagens de erro

    } catch (error) {
        console.error(error);
        //errorMessage.value = "Erro ao enviar a imagem.";
    }
};

const getCategorie = () => {
    const url = '/categorias'
    const promise = axios.get(url)
    renderToast(promise, 'carregando categorias ...', 'Categorias carregadas', (response) => {
        console.log(response.data)
        const dataToUtf8 = response.data.map((category) => { return { ...category, nome: utf8Decode(category.nome) } })
        categories.value = dataToUtf8
        isLoading.value = false
    })
}

onMounted(() => getCategorie())

</script>

<template>
    <Dialog :open="true" @update:open="handleDialogOpen">
        <DialogTrigger as-child>
            <NavigationMenuLink as-child class="cursor-pointer group gap-1" @select="(e) => e.preventDefault()">
                <button class="h-8 w-8 rounded-full text-white shadow-sm hover:shadow-md transition-all ">
                    <i :class="triggerIcon" class="transition-colors text-3xl"></i>
                    <span class="hidden min-[426px]:block">{{ props.triggerLabel }}</span>
                </button>
            </NavigationMenuLink>
        </DialogTrigger>
        <DialogContent class="gap-2">
            <DialogHeader>
                <DialogTitle class="leading-none flex gap-3 mr-4 text-lg">
                    <i :class="triggerIcon"></i>
                    <p class="font-semibold">{{ props.triggerLabel }}</p>
                </DialogTitle>
                <DialogDescription>
                    {{ props.triggerLabel }}
                </DialogDescription>
            </DialogHeader>
            <Form v-slot="{ meta, validate, setFieldValue, values, setValues }" as="" keep-values
                :validation-schema="toTypedSchema(formSchema)" :initial-values="productDetails || {}">
                <form form @submit="(event) => {
                    event.preventDefault()
                    validate()
                    console.log(values)
                    //onSubmit(values)
                }">
                    <div class="flex gap-3 flex-col">
                        <div class="flex gap-3">
                            <div v-bind="getRootProps()"
                                class="w-2/3 border border-info/70 border-dashed rounded-md  p-5 flex items-center justify-center">
                                <FormField v-slot="{ field }" name="img">
                                    <FormItem>
                                        <FormControl>
                                            <input v-bind="{ ...getInputProps(), ...field }" />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <span class="text-center text-info pointer-events-none select-none">
                                    Arraste e solte
                                    <p>ou clique e selecione uma imagem</p>
                                </span>
                            </div>
                            <div class="flex flex-col gap-3 w-full">
                                <FormField v-slot="{ componentField }" name="nome">
                                    <FormItem>
                                        <FormControl>
                                            <Input v-bind="componentField" type="text" />
                                        </FormControl>
                                        <FormLabel class="-top-5">
                                            Nome
                                        </FormLabel>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="descricao">
                                    <FormItem>
                                        <FormControl>
                                            <Textarea v-bind="componentField" />
                                        </FormControl>
                                        <FormLabel class="-top-5">
                                            Descrição
                                        </FormLabel>
                                        <FormMessage />
                                    </FormItem>
                                </FormField>
                                <FormField v-slot="{ componentField }" name="idCategoria">
                                    <FormItem>
                                        <Select v-bind="componentField">
                                            <FormControl>
                                                <SelectTrigger aria-label="select-categorie">
                                                    <SelectValue placeholder="Selecione uma categoria" />
                                                </SelectTrigger>
                                            </FormControl>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem v-for="categorie in categories"
                                                        :value="`${categorie.id}`">
                                                        {{ categorie.nome }}
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </FormItem>
                                </FormField>
                                <SelectCompositionProducts></SelectCompositionProducts>
                            </div>
                        </div>
                        <div>
                            <Button size="sm" type="submit" :disabled="disabledButton"
                                class="disabled:cursor-not-allowed">
                                {{ productDetails ? 'Atualiazar' : 'Cadastrar' }}
                            </Button>
                        </div>
                    </div>
                </form>
            </Form>
        </DialogContent>
    </Dialog>
</template>
<!-- idCategoria: z.string().nullable().optional(), -->
<!-- nome: z.string().nullable().optional(), -->
<!-- descricao: z.string(), -->
<!-- img: z.string(), -->
<!-- status: z.string().nullable().optional(), -->
<!-- itensComposicao: z.string(), -->
