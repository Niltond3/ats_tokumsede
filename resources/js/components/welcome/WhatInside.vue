<script setup>
import { onMounted, ref, computed } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import Aos from 'aos';
import DOMPurify from 'dompurify';

const selectedIndex = ref(0);

const whaterTypes = [
  {
    index: 0,
    url: '/images/mineral-glass-square.png',
    title: "<span class='text-[#3eafe4]'>Alkalina Leve</span>",
    ph: '/images/badge-alkalina-leve.png',
    mineralsLeft: [
      { name: 'Carbonato', formule: '21,60 mg/l' },
      { name: 'Bicarbonato', formule: '14,00 mg/l' },
      { name: 'Sulato', formule: '12,80 mg/l' },
      { name: 'Magnésio', formule: '12,50 mg/l' },
    ],
    mineralsRight: [
      { name: 'Citrato', formule: '8,80 mg/l' },
      { name: 'Potássio', formule: '4,40 mg/l' },
      { name: 'Cálcio', formule: '3,60 mg/l' },
      { name: 'Cloreto', formule: '3,20 mg/l' },
    ],
  },
  {
    index: 1,
    url: '/images/mineral-glass-square-rica.png',
    title: "<span class='text-[#88CB76]'>Alkalina Rica</span>",
    ph: '/images/badge-alkalina-rica.png',
    mineralsLeft: [
      { name: 'Carbonato', formule: '53,80 mg/l' },
      { name: 'Bicarbonato', formule: '35,20 mg/l' },
      { name: 'Sulato', formule: '31,80 mg/l' },
      { name: 'Magnésio', formule: '30,20 mg/l' },
    ],
    mineralsRight: [
      { name: 'Citrato', formule: '21,80 mg/l' },
      { name: 'Potássio', formule: '10,50 mg/l' },
      { name: 'Cálcio', formule: '8,90 mg/l' },
      { name: 'Cloreto', formule: '8,20 mg/l' },
    ],
  },
  {
    index: 2,
    url: '/images/mineral-glass-square-sport.png',
    title: "<span class='text-[#D5CF2B]'>Alkalina Sport</span>",
    ph: '/images/badge-alkalina-sport.png',
    mineralsLeft: [
      { name: 'Carbonato', formule: '71,00 mg/l' },
      { name: 'Bicarbonato', formule: '50,00 mg/l' },
      { name: 'Sulato', formule: '13,65 mg/l' },
      { name: 'Magnésio', formule: '40,12 mg/l' },
    ],
    mineralsRight: [
      { name: 'Citrato', formule: '30,05 mg/l' },
      { name: 'Potássio', formule: '20,01 mg/l' },
      { name: 'Cálcio', formule: '10,07 mg/l' },
      { name: 'Cloreto', formule: '14,60 mg/l' },
    ],
  },
];

const sanitizedTitle = computed(() => {
  return (index) => DOMPurify.sanitize(whaterTypes[index].title);
});

// Lógica de navegação modificada para ser modular
const handlePrev = () => {
  selectedIndex.value = (selectedIndex.value + 1) % whaterTypes.length;
};
const handleNext = () => {
  selectedIndex.value = (selectedIndex.value - 1 + whaterTypes.length) % whaterTypes.length;
};

// NOVA FUNÇÃO: Determina as classes de transformação para os títulos
const getTitleClass = (index) => {
  const totalItems = whaterTypes.length;
  const diff = index - selectedIndex.value;

  const classMap = new Map([
    [0, '[--x:0] [--y:0] [--z:0] [--rY:0] [--s:1] z-20 opacity-100'],
    [1, '[--x:80%] [--y:0] [--z:-150px] [--rY:-45deg] [--s:0.75] z-10 opacity-50 blur-sm'], // Próximo item (direita)
    [-1, '[--x:-80%] [--y:0] [--z:-150px] [--rY:45deg] [--s:0.75] z-10 opacity-50 blur-sm'], // Item anterior (esquerda)
  ]);

  let normalizedDiff = diff;
  if (diff === totalItems - 1) normalizedDiff = -1;
  if (diff === -(totalItems - 1)) normalizedDiff = 1;

  return classMap.get(normalizedDiff) || '[--s:0.5] z-0 opacity-0 pointer-events-none';
};

onMounted(() => {
  Aos.init();
});
</script>

<template>
  <div class="relative w-full p-4 my-40 min-[425px]:my-44 md:my-48 lg:my-60 xl:my-96 flex">
    <div
      v-for="{ index, url, ph, mineralsLeft, mineralsRight } in whaterTypes"
      :key="index"
      class="border-none absolute shadow-none bg-transparent *:shadow-none *:bg-transparent left-1/2 -translate-x-1/2 -translate-y-1/2 top-1/2 transition-opacity duration-300"
      :class="selectedIndex === index ? 'opacity-100' : 'opacity-0 pointer-events-none'"
    >
      <!-- O título original foi removido daqui para evitar duplicação -->
      <Card class="w-full h-full border-none px-8">
        <CardContent class="flex w-full h-full p-0">
          <!-- Coluna Esquerda (seu código original) -->
          <div
            class="w-min text-[12px] gap-3 flex flex-col min-[375px]:[&_h4]:text-base min-[375px]:[&_h6]:text-xs min-[626px]:[&_h4]:text-lg min-[626px]:[&_h6]:text-sm lg:[&_h4]:text-xl lg:[&_h6]:text-[0.8rem] justify-between xl:[&_h4]:text-2xl xl:[&_h6]:text-[1rem]"
          >
            <div
              v-for="mineral in mineralsLeft"
              :key="mineral.name"
              class="relative color-black subcolor-main transform-default *:text-left"
            >
              <h4 class="text-accepted text-center z-10 relative text-xs font-serif">
                {{ mineral.name }}
              </h4>
              <h6 class="text-success text-center mt-0 z-10 relative text-[0.5rem] font-serif">
                {{ mineral.formule }}
              </h6>
            </div>
          </div>

          <!-- Imagem Central e Botões (seu código original, com lógica de navegação atualizada) -->
          <div
            class="relative w-44 min-[425px]:w-56 min-[626px]:w-72 z-20 top-5 lg:w-96 xl:w-[35rem]"
          >
            <figure class="mb-0 inline-block align-top m-0 max-w-full">
              <div
                class="inline-block align-top max-w-full intersect:animate-fade-down intersect-full relative"
              >
                <img
                  :src="ph"
                  class="h-6 min-[425px]:h-10 min-[425px]:-top-5 md:right-16 md:-top-4 lg:h-14 lg:right-20 lg:-top-6 xl:h-16 xl:right-28 xl:-top-8 absolute right-10 z-50 -top-3 min-[425px]:right-12"
                  alt="PH level"
                />
                <img
                  width="760"
                  height="561"
                  :src="url"
                  class="relative vc_single_image-img attachment-full h-auto max-w-full align-top -top-0"
                  alt="Water type"
                  sizes="(max-width: 760px) 100vw, 760px"
                />
              </div>
            </figure>
            <div class="intersect:animate-jump-in intersect-full z-[-1] relative">
              <!-- =============================================================== -->
              <!-- INÍCIO DA SEÇÃO NOVA: CARROSSEL DE TÍTULOS CURVOS (SOBREPOSIÇÃO) -->
              <!-- =============================================================== -->
              <div
                class="absolute left-1/2 -translate-x-1/2 translate-y-[-100%] -top-32 2xsm:-top-44 sm:-top-52 min-[625px]:-top-52 md:-top-56 lg:-top-80 xl:top-[-29rem] h-24 flex items-center justify-center [transform-style:preserve-3d] [perspective:800px]"
              >
                <div class="text-center">
                  <h3 class="text-[0.7rem] md:text-sm text-info mb-1 text-nowrap">
                    Composição mineral da:
                  </h3>

                  <!-- O container real do carrossel de títulos -->
                  <div class="relative h-12 w-full flex items-center justify-center">
                    <div
                      v-for="item in whaterTypes"
                      :key="item.index"
                      :class="getTitleClass(item.index)"
                      class="absolute transition-all duration-500 ease-in-out [transform:translateX(var(--x))_translateY(var(--y))_translateZ(var(--z))_rotateY(var(--rY))_scale(var(--s))]"
                    >
                      <h4
                        class="text-xs min-[425px]:text-sm md:text-lg lg:text-2xl whitespace-nowrap cursor-pointer"
                        @click="selectedIndex = item.index"
                        v-html="sanitizedTitle(item.index)"
                      ></h4>
                      <!-- O fundo do título agora fica aqui para se mover junto -->
                      <img
                        class="w-10 mx-auto"
                        src="/images/heading-bg.png"
                        alt="Heading background"
                      />
                    </div>
                  </div>
                </div>
                <!-- Botões usam a nova lógica handleNext/handlePrev -->
                <button
                  class="absolute top-[47%] text-info/70 -translate-y-1/2 -right-1 min-[768px]:-right-28 min-[768px]:text-xl lg:text-3xl lg:-right-32 xl:-right-40 animate-jump"
                  @click="handlePrev"
                >
                  <i class="ri-arrow-right-wide-line"></i>
                </button>
                <button
                  class="absolute top-[47%] text-info/70 -translate-y-1/2 -left-1 min-[768px]:-left-36 min-[768px]:text-xl lg:text-3xl lg:-left-40 xl:-left-48 intersect:animate-jump intersect-full animate-ease-in-out"
                  @click="handleNext"
                >
                  <i class="ri-arrow-left-wide-line"></i>
                </button>
              </div>
              <!-- =============================================================== -->
              <!-- FIM DA SEÇÃO NOVA                                               -->
              <!-- =============================================================== -->
              <figure class="mb-0 inline-block align-top m-0 max-w-full">
                <div class="inline-block align-top max-w-full">
                  <img
                    width="807"
                    height="669"
                    src="/images/mineral-elements.png"
                    class="vc_single_image-img attachment-full h-auto max-w-full align-top absolute top-1/2 left-1/2 -translate-x-1/2 translate-y-[-100%]"
                    alt="Mineral elements"
                    sizes="(max-width: 807px) 100vw, 807px"
                  />
                </div>
              </figure>
            </div>
          </div>

          <!-- Coluna Direita (seu código original) -->
          <div
            class="w-min text-left text-[12px] gap-3 flex flex-col min-[375px]:[&_h4]:text-base min-[375px]:[&_h6]:text-xs min-[626px]:[&_h4]:text-lg min-[626px]:[&_h6]:text-sm lg:[&_h4]:text-xl lg:[&_h6]:text-[0.8rem] justify-between xl:[&_h6]:text-[1rem]"
          >
            <div
              v-for="mineral in mineralsRight"
              :key="mineral.name"
              class="relative color-black subcolor-main transform-default *:text-right"
            >
              <h4 class="text-accepted text-center z-10 relative text-xs font-serif">
                {{ mineral.name }}
              </h4>
              <h6 class="text-success text-center z-10 relative text-[0.5rem] font-serif">
                {{ mineral.formule }}
              </h6>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
