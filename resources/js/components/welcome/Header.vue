<script setup>
import AtsLogo from '@/../../public/images/ats-logo.svg?raw';
import Button from '@/components/Button.vue'
import { RiArrowRightWideLine as ArrowRightIcon } from "vue-remix-icons";
import { RiShoppingCartLine as ShoppingCart } from "vue-remix-icons";
import { RiInformation2Line as InformationIcon } from "vue-remix-icons";

</script>

<template>
    <div
        class="relative overflow-hidden pt-7 pb-10 bg-blue-900 text-blue-100 after:absolute after:w-full after:z-10 after:h-20 after:bottom-[-1px] after:bg-no-repeat after:bg-[length:100%_100%] after:bg-hero-mobile md:pt-10 md:pb-0 lg:pt-12 lg:after:h-[14.75rem] lg:after:bg-hero-desktop">
        <div class="container relative flex items-start flex-col">
            <div v-html="AtsLogo"></div>
            <div
                class="relative py-8 grid grid-cols-3 gap-x-3 gap-y-7 sm:grid-cols-2 sm:py-10 md:gap-10 md:auto-rows-auto lg:pt-[7.5rem] lg:pb-[8.75rem]">
                <div class="col-span-2 relative z-20  sm:col-span-1">
                    <h1
                        class="text-[1.75rem] leading-8 text-white m-0 [text-wrap:balance] md:text-[2rem] lg:text-5xl lg:leading-[3.5rem] intersect:animate-fade-right duration-1000 delay-300 animate-ease-in-out intersect-full">
                        O seu delivery de águas alcalinas</h1>
                    <div class="mt-5 md:mt-7 lg:text-lg lg:mt-8">
                        <p
                            class="p1 intersect:animate-fade-right duration-500 delay-200 animate-ease-in-out intersect-full">
                            Mais comodidade, e agilidade na entrega do seu hidratante</p>
                    </div>
                </div>
                <div class="relative sm:row-span-2">
                    <img fetchpriority="high" decoding="async" width="613" height="840"
                        src="https://www.advancedwaterinfrastructure.co.uk/wp-content/uploads/2023/08/reservoir-613x840.jpg"
                        class="intersect:animate-fade-down intersect-full animate-ease-in-out drop-mask absolute left-0 bottom-0 w-auto h-full max-w-none aspect-[613/840] z-20 sm:left-10 sm:h-[calc(100%+9.25rem)] md:h-[calc(100%+11.25rem)] lg:left-[3.75rem] lg:-bottom-[8.75rem] lg:h-[calc(100%+28.75rem)]"
                        alt=""
                        srcset="https://www.advancedwaterinfrastructure.co.uk/wp-content/uploads/2023/08/reservoir-613x840.jpg 613w, https://www.advancedwaterinfrastructure.co.uk/wp-content/uploads/2023/08/reservoir-505x692.jpg 505w"
                        sizes="(max-width: 613px) 100vw, 613px">
                    <div
                        class="bg-hero-backdrop bg-no-repeat bg-contain absolute left-0 bottom-0 w-auto h-[var(--ats-hero-backdrop-height)] aspect-[1024/1600] z-10 -translate-x-1/4 translate-y-[var(--ats-hero-backdrop-translate-y)] sm:left-10 sm:h-[calc(var(--ats-hero-backdrop-height)+18.5rem)] md:h-[calc(var(--ats-hero-backdrop-height)+22.5rem)] lg:left-[3.55rem] lg:-bottom-[9.5rem] lg:h-[calc(var(--ats-hero-backdrop-height)+67.75rem)]">
                    </div>
                </div>
                <div
                    class="col-span-3 inline-flex items-start gap-x-3 relative z-20 sm:col-span-1 opacity-0 intersect:animate-fade intersect-full duration-1000 delay-300 animate-ease-in-out">
                    <Button href="#quemsomos">
                        Quem Somos
                    </Button>
                    <Button :href="route('cliente.dashboard')" class="btn--primary">
                        <i class="icon icon--chevron-right text-xl">
                            <ShoppingCart />
                        </i>
                        <span class="hidden min-[768px]:block">Peça já</span>
                    </Button>
                    <Button href="#informations" class="btn">
                        <span class="hidden min-[768px]:block">Informações</span>
                        <i class="icon icon--chevron-right text-xl hidden min-[425px]:block">
                            <ArrowRightIcon />
                        </i>
                        <i class="icon block text-xl min-[425px]:hidden">
                            <InformationIcon />
                        </i>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
