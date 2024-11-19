<script setup>
import { onMounted, ref } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import Aos from 'aos'

const selectedIndex = ref(0)

const whaterTypes = [
    {
        index: 0,
        url: "/images/mineral-glass-square.png",
        title: "<span class='text-[#3eafe4]'>Alkalina Leve</span>",
        ph: '/images/badge-alkalina-leve.png',
        mineralsLeft: [
            {
                name: 'Carbonato',
                formule: 'CO<sub>3</sub><sup>2-</sup>',
            },
            {
                name: 'Bicarbonato',
                formule: 'HCO<sub>3-</sub>',
            },
            {
                name: 'Sulato',
                formule: 'SO<sub>4</sub><sup>2-</sup>',
            },
            {
                name: 'Magnésio',
                formule: 'MG<sup>2</sup>',
            },
        ],
        mineralsRight: [
            {
                name: 'Citrato',
                formule: '[C<sub>6</sub>H<sub>5</sub>O<sub>7</sub><sup>]3</sup>',
            },
            {
                name: 'Potássio',
                formule: 'K',
            },
            {
                name: 'Cálcio',
                formule: 'CA',
            },
            {
                name: 'Cloreto',
                formule: 'NACl',
            },
        ]
    },
    {
        index: 1,
        url: "/images/mineral-glass-square-rica.png",
        title: "<span class='text-[#88CB76]'>Alkalina Rica</span>",
        ph: '/images/badge-alkalina-rica.png',
        mineralsLeft: [
            {
                name: 'Carbonato',
                formule: 'CO<sub>3</sub><sup>2-</sup>',
            },
            {
                name: 'Bicarbonato',
                formule: 'HCO<sub>3-</sub>',
            },
            {
                name: 'Sulato',
                formule: 'SO<sub>4</sub><sup>2-</sup>',
            },
            {
                name: 'Magnésio',
                formule: 'MG<sup>2</sup>',
            },
        ],
        mineralsRight: [
            {
                name: 'Citrato',
                formule: '[C<sub>6</sub>H<sub>5</sub>O<sub>7</sub><sup>]3</sup>',
            },
            {
                name: 'Potássio',
                formule: 'K',
            },
            {
                name: 'Cálcio',
                formule: 'CA',
            },
            {
                name: 'Cloreto',
                formule: 'NACl',
            },
        ]
    },
    {
        index: 2,
        url: "/images/mineral-glass-square-sport.png",
        title: "<span class='text-[#D5CF2B]'>Alkalina Sport</span>",
        ph: '/images/badge-alkalina-sport.png',
        mineralsLeft: [
            {
                name: 'Carbonato',
                formule: 'CO<sub>3</sub><sup>2-</sup>',
            },
            {
                name: 'Bicarbonato',
                formule: 'HCO<sub>3-</sub>',
            },
            {
                name: 'Sulato',
                formule: 'SO<sub>4</sub><sup>2-</sup>',
            },
            {
                name: 'Magnésio',
                formule: 'MG<sup>2</sup>',
            },
        ],
        mineralsRight: [
            {
                name: 'Citrato',
                formule: '[C<sub>6</sub>H<sub>5</sub>O<sub>7</sub><sup>]3</sup>',
            },
            {
                name: 'Potássio',
                formule: 'K',
            },
            {
                name: 'Cálcio',
                formule: 'CA',
            },
            {
                name: 'Cloreto',
                formule: 'NACl',
            },
        ]
    },
]

onMounted(() => {
    Aos.init()
})
const handlePrev = () => {
    selectedIndex.value < 2 ? selectedIndex.value++ : selectedIndex.value = 0
    console.log(selectedIndex.value)
}
const handleNext = () => {
    selectedIndex.value > 0 ? selectedIndex.value-- : selectedIndex.value = 2
    console.log(selectedIndex.value)
}

</script>
<template>
    <div class="relative w-full p-4 my-40 min-[425px]:my-44 md:my-48 lg:my-60 xl:my-96 flex">
        <div v-for="{ index, url, ph, title, mineralsLeft, mineralsRight } in whaterTypes" :key="index"
            class="border-none absolute shadow-none bg-transparent *:shadow-none *:bg-transparent left-1/2  -translate-x-1/2 -translate-y-1/2 top-1/2 transition-all duration-300 [transition-behavior:allow-discrete] "
            :class="selectedIndex === index ? 'opacity-100 block' : 'opacity-0 hidden'">
            <div
                class="absolute -top-14 md:-top-16 left-[53%] -translate-x-1/2 text-black dark:text-white flex flex-col items-center">
                <h3 class="text-[0.7rem] md:text-sm text-info ">Composição mineral da:
                </h3>
                <h4 class="text-xs min-[425px]:text-sm md:text-lg lg:text-2xl" v-html="title"></h4>
                <img class="w-10" src="/images/heading-bg.png">
            </div>

            <Card class="w-full h-full border-none px-8">
                <CardContent class="flex w-full h-full p-0">
                    <div
                        class="w-min text-[12px] gap-3 flex flex-col min-[375px]:[&_h4]:text-base min-[375px]:[&_h6]:text-xs min-[626px]:[&_h4]:text-lg min-[626px]:[&_h6]:text-sm  lg:[&_h4]:text-xl lg:[&_h6]:text-[0.8rem] justify-between xl:[&_h4]:text-2xl xl:[&_h6]:text-[1rem]">
                        <div v-for="mineral in mineralsLeft"
                            class="relative color-black subcolor-main transform-default *:text-left"
                            id="like_sc_header_168459654">
                            <h4 class="text-accepted text-center z-10 relative text-xs font-serif">{{
                                mineral.name }}</h4>
                            <h6 class="text-success text-center mt-0 z-10 relative text-[0.5rem] font-serif"
                                v-html="mineral.formule"></h6>
                            <div class="hidden w-1/2">
                                <p>
                                    <span class="">
                                        <em>Vestibulum non nisi tincidunt, pulvinar nibh sed,
                                            accumsan dui. In purus dolor.
                                        </em>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="relative w-44 min-[425px]:w-56 min-[626px]:w-72 z-20 top-5 lg:w-96 xl:w-[35rem]">
                        <figure class="mb-0 inline-block align-top m-0 max-w-full ">
                            <div
                                class="inline-block align-top max-w-full intersect:animate-fade-down intersect-full relative">
                                <img :src="ph"
                                    class="h-6 min-[425px]:h-10 min-[425px]:-top-5 md:right-16 md:-top-4 lg:h-14 lg:right-20 lg:-top-6 xl:h-16 xl:right-28 xl:-top-8 absolute right-10 z-50 -top-3 min-[425px]:right-12 ">
                                <img width="760" height="561" :src="url"
                                    class=" relative vc_single_image-img attachment-full h-auto max-w-full align-top -top-0 "
                                    alt="" sizes="(max-width: 760px) 100vw, 760px">
                            </div>
                        </figure>
                        <div class="intersect:animate-jump-in intersect-full z-[-1] ">
                            <figure class="mb-0 inline-block align-top m-0 max-w-full ">
                                <div class="inline-block align-top max-w-full ">
                                    <img width="807" height="669" src="/images/mineral-elements.png"
                                        class="vc_single_image-img attachment-full h-auto max-w-full align-top absolute top-1/2 left-1/2 -translate-x-1/2 translate-y-[-100%]"
                                        alt="" sizes="(max-width: 807px) 100vw, 807px">
                                </div>
                            </figure>
                        </div>
                        <button
                            class="absolute top-[43%] text-info/70 -translate-y-1/2 right-0 min-[425px]:-right-1 min-[768px]:-right-28 min-[768px]:text-xl lg:text-3xl lg:-right-32 xl:-right-40 animate-jump"
                            @click="handlePrev">
                            <i class="ri-arrow-right-wide-line"></i>
                        </button>
                        <button
                            class="absolute top-[43%] text-info/70 -translate-y-1/2 min-[425px]:-left-1 min-[768px]:-left-36 min-[768px]:text-xl lg:text-3xl lg:-left-40 xl:-left-48 intersect:animate-jump intersect-full animate-ease-in-out"
                            @click="handleNext">
                            <i class="ri-arrow-left-wide-line"></i>
                        </button>
                    </div>
                    <div
                        class="w-min text-left text-[12px] gap-3 flex flex-col min-[375px]:[&_h4]:text-base min-[375px]:[&_h6]:text-xs min-[626px]:[&_h4]:text-lg min-[626px]:[&_h6]:text-sm lg:[&_h4]:text-xl lg:[&_h6]:text-[0.8rem] justify-between xl:[&_h6]:text-[1rem]">
                        <div v-for="mineral in mineralsRight"
                            class="relative  color-black subcolor-main transform-default *:text-right"
                            id="like_sc_header_168459654">
                            <h4 class="text-accepted text-center z-10 relative text-xs font-serif">{{
                                mineral.name }}</h4>
                            <h6 class="text-success text-center z-10 relative text-[0.5rem] font-serif"
                                v-html="mineral.formule">
                            </h6>
                            <div class="hidden">
                                <p>
                                    <span class="">
                                        <em>Vestibulum non nisi tincidunt, pulvinar nibh sed,
                                            accumsan dui. In purus dolor.
                                        </em>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
