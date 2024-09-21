<script setup>
import { ref } from 'vue'
import { watchOnce } from '@vueuse/core'
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel'
import { Card, CardContent } from '@/components/ui/card'

const emblaMainApi = ref()
const emblaThumbnailApi = ref()
const selectedIndex = ref(0)

const certificates = [
    {index:0, url: "/images/certificates/aesa.png"},
    {index:1, url: "/images/certificates/agevisa.png"},
    {index:2, url: "/images/certificates/Alvara-funcionamento-municipal.png"},
    {index:3, url: "/images/certificates/conselho-de-quimica.png"},
    {index:4, url: "/images/certificates/Sudema.png"}
]

function onSelect() {
  if (!emblaMainApi.value || !emblaThumbnailApi.value)
    return
  selectedIndex.value = emblaMainApi.value.selectedScrollSnap()
  emblaThumbnailApi.value.scrollTo(emblaMainApi.value.selectedScrollSnap())
}

function onThumbClick(index) {
    console.log(emblaMainApi.value)
  if (!emblaMainApi.value || !emblaThumbnailApi.value)
    return
  emblaMainApi.value.scrollTo(index)
}

watchOnce(emblaMainApi, (emblaMainApi) => {
  if (!emblaMainApi)
    return

  onSelect()
  emblaMainApi.on('select', onSelect)
  emblaMainApi.on('reInit', onSelect)
})
</script>

<template>
  <div class="w-full sm:w-auto flex items-center flex-col">
    <Carousel
      class="relative w-full max-w-xs"
      @init-api="(val) => emblaMainApi = val"
    >
      <CarouselContent>
        <CarouselItem v-for="{index,url} in certificates" :key="index">
          <div class="p-1">
            <Card class="border-[#00639a]">
              <CardContent class="flex aspect-square items-center justify-center p-6">
                <img :src="url"></img>
              </CardContent>
            </Card>
          </div>
        </CarouselItem>
      </CarouselContent>
      <CarouselPrevious class="border-sky-300 border-2 text-sky-200 hover:text-sky-600 transition-colors"/>
      <CarouselNext class="border-sky-300 border-2 text-sky-200 hover:text-sky-600 transition-colors"/>
    </Carousel>

    <Carousel
      class="relative w-full max-w-xs"
      @init-api="(val) => emblaThumbnailApi = val"
    >
      <CarouselContent class="flex gap-1 ml-0">
        <CarouselItem v-for="{index, url} in certificates" :key="index" class="pl-0 basis-1/4 cursor-pointer" @click="onThumbClick(index)">
          <div class="p-1" :class="index === selectedIndex ? '' : 'opacity-50'">
            <Card>
              <CardContent class="flex aspect-square items-center justify-center p-6">
                <img :src="url"></img>
              </CardContent>
            </Card>
          </div>
        </CarouselItem>
      </CarouselContent>
    </Carousel>
  </div>
</template>
