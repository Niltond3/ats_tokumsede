<script setup>
// Vue core
import { ref, onMounted } from 'vue';

// Form validation
import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

// UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Form, FormField } from '@/components/ui/form';
import { FormItem, FormLabel, FormControl, FormMessage } from '../../ui/form';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import DialogTrigger from '../DialogTrigger.vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Link } from '@inertiajs/vue3';

// Utilities
import { useDropzone } from 'vue3-dropzone';
import { dialogState } from '@/hooks/useToggleDialog';
import renderToast from '../../renderPromiseToast';
import { StringUtil } from '@/util';
import SelectImages from './SelectImages.vue';
import SelectCompositionProducts from './SelectCompositionProducts.vue';
import { event } from 'jquery';

const isLoading = ref(true);
const categories = ref([]);
const imagesSrc = ref([]);
const productDetails = ref();
const itensComposicao = ref('');
const defaultImgValue = {
  src: null,
  raw: null,
};
const img = ref(defaultImgValue);

const { isOpen, toggleDialog } = dialogState('RegisterProduct');

const { getRootProps, getInputProps, open, ...rest } = useDropzone({ onDrop, multiple: false });

const disabledButton = ref(false);

const formSchema = z.object({
  nome: z.string({ required_error: 'Informe o nome do produto' }),
  idCategoria: z.string({ required_error: 'Informe a categoria do produto' }),
  descricao: z.string({ required_error: 'Descreva o produto' }),
  img: z.string({ required_error: 'Selecione uma imagem' }),
  itensComposicao: z.array(z.string()).nullable().optional(),
});

const emits = defineEmits(['on:create', 'update:dialogOpen']);

const handleDialogOpen = (op) => {
  !op && emits('update:dialogOpen', false);
  toggleDialog();
};

function onDrop(acceptFiles, rejectReasons) {
  const src = URL.createObjectURL(acceptFiles[0]);
  img.value = {
    src,
    raw: acceptFiles,
  };
}

const getCategorie = () => {
  const url = '/categorias';
  const promise = axios.get(url);
  renderToast(
    promise,
    'carregando categorias ...',
    'Categorias carregadas',
    'Ocorreu um erro ao carregar as categorias',
    (response) => {
      const dataToUtf8 = response.data.map((category) => {
        return { ...category, nome: StringUtil.utf8Decode(category.nome) };
      });
      categories.value = dataToUtf8;
      isLoading.value = false;
    },
  );
};

const getImages = () => {
  const url = '/api/listImages';
  const promise = axios.get(url);
  renderToast(
    promise,
    'carregando imagens ...',
    'Imagens carregadas',
    'Ocorreu um erro ao carregar as imagens',
    (response) => {
      const imageNames = response.data.img;
      imagesSrc.value = imageNames.map((name) => `images/uploads/${name}`);
    },
  );
};
onMounted(() => {
  getCategorie();
  getImages();
});

const handleDefaultImage = () => (img.value = defaultImgValue);

const registerProduct = (payload) => {
  const url = '/produtos';
  const promise = axios.post(url, payload);
  renderToast(
    promise,
    'Salvando produto ...',
    'Produto salvo',
    'Ocorreu um erro ao salvar o produto',
  );
};
const uploadImage = (image, payload) => {
  const formData = new FormData();
  formData.append('image', image[0]);

  const promise = axios.post('/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });

  renderToast(
    promise,
    'Salvando imagem ...',
    'Imagem salva',
    'Ocorreu um erro ao salvar a imagem',
    (response) => {
      // Exibe a imagem carregada
      const img = response.data.fineName;
      registerProduct({
        ...payload,
        img,
      });
    },
  );
};

const onSubmit = (values) => {
  const fileImage = img.value.raw;

  const payload = {
    ...values,
    composicao: values.itensComposicao?.length > 0 ? 1 : 0,
  };

  if (fileImage) return uploadImage(fileImage, payload);
  registerProduct(payload);
};

const handleSubmit = async (event, values, validate) => {
  event.preventDefault();
  const isValid = await validate();
  if (isValid.valid) onSubmit(values);
};

const handleImageLoad = (setFieldValue) => {
  const fileImage = img.value.raw;
  if (fileImage) setFieldValue('img', fileImage[0].name);
};
</script>

<template>
  <Dialog :open="isOpen" @update:open="handleDialogOpen">
    <DialogTrigger icon="ri-shopping-bag-3-fill" title="Produto" />
    <DialogContent class="gap-2">
      <DialogHeader>
        <DialogTitle class="leading-none flex gap-3 mr-4 text-lg text-info">
          <i class="ri-shopping-bag-3-fill"></i>
          <span class="hidden min-[426px]:block">Produto</span>
        </DialogTitle>
        <DialogDescription class="py-2"> Cadastro de produto </DialogDescription>
      </DialogHeader>
      <Form
        v-slot="{ validate, setFieldValue, values }"
        as=""
        keep-values
        :validation-schema="toTypedSchema(formSchema)"
        :initial-values="productDetails || {}"
      >
        <form form @submit="(event) => handleSubmit(event, values, validate)">
          <div class="flex gap-3 flex-col">
            <div class="flex gap-3 flex-col sm:flex-row">
              <FormField name="img">
                <div class="flex flex-col gap-3 sm:w-2/3">
                  <SelectImages
                    :disabled-button="!!img.src"
                    @image-selected="(img) => setFieldValue('img', img)"
                  />
                  <div class="relative">
                    <div
                      v-bind="getRootProps()"
                      class="border border-info/70 border-dashed rounded-md cursor-pointer p-5 flex items-center justify-center relative overflow-hidden h-[182px]"
                    >
                      <input v-bind="getInputProps()" />
                      <img
                        v-if="img.src"
                        :src="img.src"
                        alt="preview"
                        class="absolute inset-0 h-32 w-auto object-cover top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
                        @load="() => handleImageLoad(setFieldValue)"
                      />
                      <span
                        v-if="!img.src"
                        class="text-center text-info pointer-events-none select-none"
                      >
                        Arraste e solte
                        <p>ou clique e selecione uma imagem</p>
                      </span>
                    </div>
                    <button
                      v-if="img.src"
                      class="absolute top-2 right-2 z-50 text-danger text-3xl"
                      @click="
                        () => {
                          handleDefaultImage();
                          setFieldValue('img', undefined);
                        }
                      "
                    >
                      <i class="ri-close-circle-fill"></i>
                    </button>
                  </div>
                  <FormMessage />
                </div>
              </FormField>
              <div class="flex flex-col gap-3 w-full">
                <FormField v-slot="{ componentField }" name="nome">
                  <FormItem>
                    <FormControl>
                      <Input v-bind="componentField" type="text" />
                    </FormControl>
                    <FormLabel class="-top-5"> Nome </FormLabel>
                    <FormMessage />
                  </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="descricao">
                  <FormItem>
                    <FormControl>
                      <Textarea v-bind="componentField" />
                    </FormControl>
                    <FormLabel class="-top-5"> Descrição </FormLabel>
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
                          <SelectItem
                            v-for="categorie in categories"
                            :key="categorie.id"
                            :value="`${categorie.id}`"
                          >
                            {{ categorie.nome }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                </FormField>
                <SelectCompositionProducts
                  @update:model-value="(val) => setFieldValue('itensComposicao', val)"
                >
                </SelectCompositionProducts>
              </div>
            </div>
            <div>
              <Button
                size="sm"
                type="submit"
                :disabled="disabledButton"
                class="disabled:cursor-not-allowed"
              >
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
