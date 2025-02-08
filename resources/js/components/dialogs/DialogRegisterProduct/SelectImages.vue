<template>
  <div>
    <UseTemplate>
      <Command>
        <CommandInput placeholder="imagens..." :disabled="props.disabledButton" class="mb-1" />
        <CommandList>
          <CommandEmpty>Nenhuma imagem encontrada.</CommandEmpty>
          <CommandGroup>
            <CommandItem
              v-for="image of images"
              :key="image.name"
              :value="image.name"
              @select="selectImage(image)"
            >
              <div class="flex gap-2">
                <img :src="image.src" alt="" class="h-6 w-6 rounded-md object-cover" />
                <span class="block truncate">{{ image.name }}</span>
              </div>
            </CommandItem>
          </CommandGroup>
        </CommandList>
      </Command>
    </UseTemplate>

    <Popover v-if="isDesktop" :open="isOpen">
      <PopoverTrigger as-child>
        <button
          class="text-info w-[174px] rounded-md border border-input h-9 disabled:opacity-60 disabled:cursor-not-allowed"
          :disabled="props.disabledButton"
        >
          <div v-if="selectedImage" class="flex gap-2">
            <img :src="selectedImage.src" alt="" class="h-6 w-6 rounded-md object-cover" />
            <span class="block truncate">{{ selectedImage.name }}</span>
          </div>
          <p v-else>+ Imagens</p>
        </button>
      </PopoverTrigger>
      <PopoverContent class="p-0" align="start">
        <StatusList />
      </PopoverContent>
    </Popover>

    <Drawer v-else :open="isOpen">
      <DrawerTrigger as-child>
        <button
          class="text-info w-[174px] rounded-md border border-input h-9 disabled:opacity-60 disabled:cursor-not-allowed"
          :disabled="props.disabledButton"
        >
          <div v-if="selectedImage" class="flex gap-2">
            <img :src="selectedImage.src" alt="" class="h-6 w-6 rounded-md object-cover" />
            <span class="block truncate">{{ selectedImage.name }}</span>
          </div>
          <p v-else>+ Imagens</p>
        </button>
      </DrawerTrigger>
      <DrawerContent>
        <div class="mt-4 border-t">
          <StatusList />
        </div>
      </DrawerContent>
    </Drawer>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';
import { Drawer, DrawerContent, DrawerTrigger } from '@/components/ui/drawer';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { createReusableTemplate, useMediaQuery } from '@vueuse/core';
import renderToast from '@/components/renderPromiseToast';

const selectedImage = ref(null);
const images = ref([]);

const emits = defineEmits(['image-selected']);
const props = defineProps({ disabledButton: Boolean });

const [UseTemplate, StatusList] = createReusableTemplate();
const isDesktop = useMediaQuery('(min-width: 768px)');

const isOpen = ref(false);

const getImages = () => {
  const url = '/api/listImages';
  const promise = axios.get(url);
  renderToast(
    promise,
    'carregando imagens ...',
    'Imagens carregadas',
    'Erro ao carregar imagens',
    (response) => {
      const imageNames = response.data.img;

      images.value = imageNames.map((name) => {
        return {
          name: name,
          src: `images/uploads/${name}`,
        };
      });
    },
  );
};

watch(
  () => props.disabledButton,
  (newValue, oldValue) => {
    if (newValue) cleanSelectImage();
  },
);

const selectImage = (image) => {
  isOpen.value = false;
  selectedImage.value = image;
  emits('image-selected', image.name);
};

onMounted(() => {
  getImages();
});

const cleanSelectImage = () => {
  //selectImageRef.reset()
  selectedImage.value = null;
  emits('image-selected', undefined);
};
</script>
