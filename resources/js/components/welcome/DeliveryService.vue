<script setup>
import { onMounted, ref } from 'vue';
import Button from '@/components/Button.vue';
import { RiShoppingCartLine as ShoppingCart } from 'vue-remix-icons';
import Separator from '../ui/separator/Separator.vue';
import Aos from 'aos';

onMounted(() => {
  Aos.init();
});
</script>
<template>
  <section
    class="bg-success relative before:bg-bg-cut before:absolute before:h-4 before:w-full before:z-10 before:top-0 before:rotate-180"
  >
    <div class="w-full bg-accepted flex flex-col min-[450px]:flex-row items-center">
      <img src="/images/delivery.png " class="min-[450px]:h-36 md:h-72 lg:h-auto" />
      <div class="p-4 gap-4 flex flex-col">
        <p class="text-3xl font-bold font-serif">
          <span class="text-success">Serviço de</span><span class="text-white"> Delivery</span>
        </p>
        <p class="py-2 text-white/70">
          Nossos entregadores estão focados na entrega de produtos com qualidade e segurança.
        </p>
        <div class="flex flex-col gap-2 font-bold text-success">
          <span>
            <i class="ri-check-fill text-green-400"></i>
            Entrega grátis
          </span>
          <span>
            <i class="ri-check-fill text-green-400"></i>
            De segunda à Sábado
          </span>
          <span>
            <i class="ri-check-fill text-green-400"></i>
            8:00 - 17:00
          </span>
        </div>
        <div>
          <Button :href="route('cliente.dashboard')" class="btn--primary bg-success">
            <i class="icon icon--chevron-right text-xl text-white">
              <ShoppingCart />
            </i>
            <span class="hidden min-[768px]:block">Peça já</span>
          </Button>
        </div>
      </div>
    </div>
    <div class="flex flex-col gap-3 justify-center p-2">
      <div class="text-base font-serif p-3 text-white/80 font-bold gap-4 flex flex-col">
        <p class="text-2xl font-bold text-accepted">Produto de qualidade</p>
        As nossas águas Alkalinas são ideias para beber, cozinhar, esportes e mesmo para crianças,
        idosos e pessoas com limitações.
      </div>
      <div class="flex flex-wrap py-2 gap-4 px-4 justify-center items-center">
        <div class="flex flex-col max-w-[34%]">
          <i class="ri-microscope-line text-5xl" data-aos="fade-up"></i>
          <span class="" data-aos="zoom-in"> Controle minuncioso</span>
        </div>
        <Separator orientation="vertical" class="hidden md:block h-9 bg-input"></Separator>
        <div class="flex flex-col max-w-[34%]">
          <div class="relative delay-200">
            <i class="ri-heart-2-line text-5xl delay-200" data-aos="fade-up"></i>
            <i
              class="ri-test-tube-line text-5xl absolute left-3 text-info/80 z-50 delay-200"
              data-aos="fade-up"
            ></i>
          </div>
          <span class="delay-200" data-aos="zoom-in"> Composição saudável</span>
        </div>
        <Separator orientation="vertical" class="hidden md:block h-9 bg-input"></Separator>
        <div class="flex flex-col max-w-[34%]">
          <i class="ri-filter-line text-5xl delay-500" data-aos="fade-up"></i>
          <span class="delay-500" data-aos="zoom-in"> Filtragem eficiente</span>
        </div>
        <Separator orientation="vertical" class="hidden md:block h-9 bg-input"></Separator>
        <div class="flex flex-col max-w-[34%]">
          <i class="ri-file-paper-2-line text-5xl delay-700" data-aos="fade-up"></i>
          <span class="delay-700" data-aos="zoom-in"> Qualidade certificada</span>
        </div>
      </div>
    </div>
  </section>
</template>
