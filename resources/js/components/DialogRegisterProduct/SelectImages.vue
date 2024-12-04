<template>
    <Select @update:modelValue="selectImage" :disabled="props.disabledButton" v-model="selectedImage">
        <SelectTrigger class="max-w-[174px]">
            <SelectValue placeholder="Imagens salvas" />
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectItem v-for="image in images" :key="image.name" :value="image.name">
                    <div class="flex gap-2">
                        <img :src="image.src" alt="" class="h-6 w-6 rounded-md object-cover" />
                        <span class="block truncate">{{ image.name }}</span>
                    </div>
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import renderToast from '../renderPromiseToast'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'


const selectedImage = ref(null)
const images = ref([])

const emits = defineEmits(['image-selected'])
const props = defineProps({ disabledButton: Boolean })

const getImages = () => {
    const url = '/api/listImages'
    const promise = axios.get(url)
    renderToast(promise, 'carregando imagens ...', 'Imagens carregadas', (response) => {
        const imageNames = response.data.img

        images.value = imageNames.map(name => {
            return {
                name: name,
                src: `images/uploads/${name}`
            }
        })
    })
};

watch(
    () => props.disabledButton,
    (newValue, oldValue) => {
        if (newValue) cleanSelectImage()
    }
);


const selectImage = (image) => {
    selectedImage.value = image;
    emits("image-selected", image);
}

onMounted(() => {
    getImages()
})

const cleanSelectImage = () => {
    //selectImageRef.reset()
    selectedImage.value = null
    emits("image-selected", undefined)
}
</script>
