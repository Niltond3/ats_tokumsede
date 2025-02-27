<template>
  <!-- Diálogo para cadastro/atualização de produtos -->
  <Dialog :open="isOpen" @update:open="handleDialogOpen">
    <!-- Botão que aciona o diálogo -->
    <DialogTrigger icon="ri-shopping-bag-3-fill" title="Produto" />

    <DialogContent class="gap-2">
      <div id="dialog-portal-container"></div>
      <!-- Formulário com validação integrada via Vee-Validate -->
      <Form
        v-slot="{ validate, setFieldValue, values }"
        as=""
        keep-values
        :validation-schema="toTypedSchema(formSchema)"
        :initial-values="productDetails || {}"
      >
        <DialogHeader>
          <DialogTitle class="leading-none flex gap-3 mr-4 text-lg text-info items-center">
            <div class="flex gap-3">
              <i class="ri-shopping-bag-3-fill"></i>
              <span class="hidden min-[426px]:block">Produto</span>
            </div>
            <!-- Dropdown para selecionar um produto já cadastrado -->
            <ProductsDropdown
              :products="products"
              @product-selected="(id) => handleUpdateSelect(id, setFieldValue, validate)"
            />
          </DialogTitle>
          <DialogDescription class="py-2">Cadastro de produto</DialogDescription>
        </DialogHeader>

        <!-- Início do formulário -->
        <form @submit="(event) => handleSubmit(event, values, validate)">
          <div class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row gap-3">
              <!-- Campo de upload de imagem -->
              <FormField name="img">
                <div class="flex flex-col gap-3 sm:w-2/3">
                  <SelectImages
                    :disabled-button="!!img.src"
                    @image-selected="(imgSelected) => setFieldValue('img', imgSelected)"
                  />
                  <div class="relative">
                    <div
                      v-bind="getRootProps()"
                      class="border border-info/70 border-dashed rounded-md cursor-pointer p-5 flex items-center justify-center relative overflow-hidden h-[182px]"
                    >
                      <input v-bind="getInputProps()" />
                      <!-- Exibe a pré-visualização da imagem se disponível -->
                      <img
                        v-if="img.src"
                        :src="img.src"
                        alt="preview"
                        class="absolute inset-0 h-32 w-auto object-cover top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
                        @load="() => handleImageLoad(setFieldValue)"
                      />
                      <!-- Mensagem padrão caso nenhuma imagem esteja selecionada -->
                      <span v-else class="text-center text-info pointer-events-none select-none">
                        Arraste e solte<br />ou clique para selecionar uma imagem
                      </span>
                    </div>
                    <!-- Botão para remover a imagem selecionada -->
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
                <!-- Campo para o nome do produto -->
                <FormField v-slot="{ componentField }" name="nome">
                  <FormItem>
                    <FormControl>
                      <Input v-bind="componentField" type="text" />
                    </FormControl>
                    <FormLabel class="-top-5">Nome</FormLabel>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <!-- Campo para a descrição do produto -->
                <FormField v-slot="{ componentField }" name="descricao">
                  <FormItem>
                    <FormControl>
                      <Textarea v-bind="componentField" />
                    </FormControl>
                    <FormLabel class="-top-5">Descrição</FormLabel>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <!-- Campo para seleção da categoria -->
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
                          <SelectItem v-for="cat in categories" :key="cat.id" :value="`${cat.id}`">
                            {{ cat.nome }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <!-- Componente para seleção dos itens de composição -->
                <SelectCompositionProducts
                  :initial-products="selectedProducts"
                  @update:model-value="(val) => setFieldValue('itensComposicao', val)"
                />
              </div>
            </div>

            <!-- Botão de submissão do formulário -->
            <div>
              <Button
                size="sm"
                type="submit"
                :disabled="disabledButton"
                class="disabled:cursor-not-allowed"
              >
                {{ Object.keys(productDetails).length ? 'Atualizar' : 'Cadastrar' }}
              </Button>
            </div>
          </div>
        </form>
      </Form>
    </DialogContent>
  </Dialog>
</template>

<script setup>
// Importa o helper para transformar o esquema de validação
import { toTypedSchema } from '@vee-validate/zod';
// Importa o composable que contém a lógica de produto
import { useProductRegistration } from '@/composables/useProductRegistration';

// Importa os componentes utilizados
import ProductsDropdown from '@/components/dropdowns/ProductsDropdown.vue';
import SelectImages from './SelectImages.vue';
import SelectCompositionProducts from './SelectCompositionProducts.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Form, FormField } from '@/components/ui/form';
import { FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import DialogTrigger from '@/components/dialogs/DialogTrigger.vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

// Desestrutura os estados e funções do composable
const {
  isLoading,
  categories,
  imagesSrc,
  products,
  productDetails,
  selectedProducts,
  img,
  disabledButton,
  formSchema,
  getRootProps,
  getInputProps,
  open,
  isOpen,
  handleDialogOpen,
  handleSubmit,
  handleImageLoad,
  handleUpdateSelect,
  handleDefaultImage,
} = useProductRegistration();
</script>
