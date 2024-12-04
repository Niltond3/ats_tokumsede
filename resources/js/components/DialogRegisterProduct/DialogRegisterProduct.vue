<script setup>
// Vue core
import { ref, onMounted } from 'vue'

// Form validation
import * as z from 'zod'
import { toTypedSchema } from '@vee-validate/zod'

// UI Components
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Form, FormField } from '@/components/ui/form'
import {
    FormItem,
    FormLabel,
    FormControl,
    FormMessage
} from '../ui/form'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { NavigationMenuLink } from '@/components/ui/navigation-menu'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'

// Utilities
import { useDropzone } from "vue3-dropzone"
import { dialogState } from '@/hooks/useToggleDialog'
import renderToast from '../renderPromiseToast'
import { utf8Decode } from '@/util'
import SelectImages from './SelectImages.vue'
import { event } from 'jquery'
import SelectCompositionProducts from './SelectCompositionProducts.vue'

const isLoading = ref(true)
const categories = ref([])
const imagesSrc = ref([])
const productDetails = ref()
const itensComposicao = ref('')
const defaultImgValue = {
    src: null,
    raw: null
}
const img = ref(defaultImgValue)

const { isOpen, toggleDialog } = dialogState()


const { getRootProps, getInputProps, open, ...rest } = useDropzone({ onDrop, multiple: false });

const disabledButton = ref(false)

const formSchema = z.object({
    nome: z.string(),
    idCategoria: z.string(),
    descricao: z.string(),
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
    const src = URL.createObjectURL(acceptFiles[0]);
    console.log(acceptFiles)
    img.value = {
        src,
        raw: acceptFiles,
    }
}

const getCategorie = () => {
    const url = '/categorias'
    const promise = axios.get(url)
    renderToast(promise, 'carregando categorias ...', 'Categorias carregadas', (response) => {
        const dataToUtf8 = response.data.map((category) => { return { ...category, nome: utf8Decode(category.nome) } })
        categories.value = dataToUtf8
        isLoading.value = false
    })
}

const getImages = () => {
    const url = '/api/listImages'
    const promise = axios.get(url)
    renderToast(promise, 'carregando imagens ...', 'Imagens carregadas', (response) => {
        console.log(response.data)
        const imageNames = response.data.img
        imagesSrc.value = imageNames.map(name => `images/uploads/${name}`)
    })
}
onMounted(() => {
    getCategorie()
    getImages()
})

const handleDefaultImage = () => img.value = defaultImgValue



const registerProduct = (payload) => {
    const url = '/produtos'
    const promise = axios.post(url, payload)
    renderToast(promise, 'Salvando produto ...', 'Produto salvo', (response) => {
        console.log(response.data)
    })
}
const uploadImage = (image, payload) => {
    const formData = new FormData();
    formData.append('image', image[0]);

    const promise = axios.post('/upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });

    renderToast(promise, 'Salvando imagem ...', 'Imagem salva', (response) => {
        // Exibe a imagem carregada
        const img = response.data.fineName
        registerProduct({
            ...payload,
            img
        })
    })
};

const onSubmit = (values) => {
    //setFieldValue('img', img.value.raw)
    //uploadImage(img.value.raw)

    const fileImage = img.value.raw


    console.log(values)
    const itensComposicao = JSON.stringify(values.itensComposicao).replace(/"/g, "'")
    console.log(values)
    const payload = {
        ...values,
        composicao: values.itensComposicao?.length > 0 ? 1 : 0,
    }

    if (fileImage) return uploadImage(fileImage, payload)
    console.log(payload)
    registerProduct(payload)
}
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
                    onSubmit(values, setFieldValue)
                }">
                    <div class="flex gap-3 flex-col">
                        <div class="flex gap-3 flex-col sm:flex-row">
                            <div class="flex flex-col gap-3 sm:w-2/3">
                                <SelectImages @image-selected="(img) => setFieldValue('img', img)"
                                    :disabled-button="!!img.src" />
                                <div class="relative">
                                    <div v-bind="getRootProps()"
                                        class="border border-info/70 border-dashed rounded-md cursor-pointer p-5 flex items-center justify-center relative overflow-hidden h-[182px]">
                                        <input v-bind="getInputProps()" />
                                        <img v-if="img.src" :src="img.src" alt="preview"
                                            class="absolute inset-0 h-32 w-auto object-cover top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
                                            @change="" />
                                        <span v-if="!img.src"
                                            class="text-center text-info pointer-events-none select-none">
                                            Arraste e solte
                                            <p>ou clique e selecione uma imagem</p>
                                        </span>
                                    </div>
                                    <button v-if="img.src" class="absolute top-2 right-2 z-50 text-danger text-3xl"
                                        @click="handleDefaultImage">
                                        <i class="ri-close-circle-fill"></i>
                                    </button>
                                </div>
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
                                <SelectCompositionProducts
                                    @update:model-value="(val) => setFieldValue('itensComposicao', val)">
                                </SelectCompositionProducts>
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
