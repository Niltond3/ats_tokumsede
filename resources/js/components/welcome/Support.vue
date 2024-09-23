<script setup>
import Button from '../Button.vue';
import { RiWhatsappLine as WhatsappIcon } from "vue-remix-icons";
import { RiPagesLine as WebIcon } from "vue-remix-icons";
import { RiShoppingCartLine as CartIcon } from "vue-remix-icons";
import { RiEBike2Fill as BikeIcon } from "vue-remix-icons";
import { RiFlashlightFill as FlashIcon } from "vue-remix-icons";
import CertificatesCarousel from './CertificatesCarousel.vue';

</script>

<template>
    <section class="container flex [&>*]:flex-1 gap-4 xl:px-64 mt-20 py-10" id="support">
        <div class="flex gap-7 flex-col items-center">
            <img src="/images/saiba_mais.png" class="w-min" />
            <div
                class="flex flex-col text-slate-300 [&>div]:flex [&>div]:items-center [&>div]:justify-between [&>div]:py-4 [&>div]:px-7 [&_h4]:text-xl [&_p]:text-sm [&_p]:max-w-[70%] [&_p]:text-slate-100 [&_i]:text-2xl [&>div]:relative [&>div]:after:absolute [&>div]:after:-bottom-[16px] [&>div]:after:z-10 [&>div]:after:left-1/2 [&>div]:after:-translate-x-1/2 [&>div]:after:h-0 [&>div]:after:w-0 [&>div]:after:border-x-[16px] [&>div]:after:border-b-[16px] [&>div]:after:border-x-transparent [&>div]:after:rotate-180">
                <h2 class="my-8 text-slate-700">Como Comprar?</h2>
                <div class="bg-stone-600 after:border-stone-600 gap-3">
                    <div
                        class="absolute top-[-20px] left-[-20px] bg-slate-300 text-stone-600 text-3xl font-semibold border-solid border-[5px] border-stone-600 rounded-full w-10 h-10  flex items-center justify-center">
                        1
                    </div>
                    <div>
                        <h4>Passo 1</h4>
                        <p>Escolha a melhor forma de realizar o pedido</p>
                    </div>
                    <Button class="max-md:h-14 max-md:w-14" href="/">
                        <span class="hidden min-[768px]:block">whatspp</span>
                        <i class="icon icon--chevron-right text-xl">
                            <WhatsappIcon />
                        </i>
                    </Button>
                    <Button class="max-md:h-14 max-md:w-14" href="/">
                        <span class="hidden min-[768px]:block">Site</span>
                        <i class="icon icon--chevron-right text-xl">
                            <WebIcon />
                        </i>
                    </Button>
                    <Button class="max-md:h-14 max-md:w-14" href="/">
                        <span class="hidden min-[768px]:block">App</span>
                        <img src="/images/tokumsede-app.png" class="max-w-8"></img>
                    </Button>

                </div>
                <div class="bg-sky-600 after:border-sky-600">
                    <div
                        class="absolute top-[-20px] left-[-20px] bg-slate-300 text-sky-600 text-3xl font-semibold border-solid border-[5px] border-sky-600 rounded-full w-10 h-10  flex items-center justify-center">
                        2
                    </div>
                    <div>
                        <h4>Passo 2</h4>
                        <p class="flex items-center flex-wrap">
                            <span class="flex items-center">Faça seu <a :href="route('register')">Cadastro</a>! É
                                Rápido.
                                <i class="icon text-yellow-600">
                                    <FlashIcon />
                                </i>
                            </span>

                            <span>Ou faça <a :href="route('login')">Login</a></span>
                        </p>

                    </div>
                    <p>print da tela de cadastro</p>

                </div>
                <div class="bg-emerald-600 after:border-emerald-600">
                    <div
                        class="absolute top-[-20px] left-[-20px] bg-slate-300 text-emerald-600 text-3xl font-semibold border-solid border-[5px] border-emerald-600 rounded-full w-10 h-10  flex items-center justify-center">
                        3
                    </div>
                    <div>
                        <h4>Passo 3</h4>
                        <p>Selecione a água Alkalina que atenda suas necessidades, e clique em Comprar</p>
                    </div>
                    <i class="icon">
                        <CartIcon />
                    </i>

                </div>
                <div class="bg-yellow-600 after:content-none">
                    <div
                        class="absolute top-[-20px] left-[-20px] bg-slate-300 text-yellow-600 text-3xl font-semibold border-solid border-[5px] border-yellow-600 rounded-full w-10 h-10 flex items-center justify-center">
                        4
                    </div>
                    <div>
                        <h4>Passo 4</h4>
                        <p>Confira seu pedido, selecione a forma de pagamento e clique em Finalizar. Agora é só esperar
                        </p>
                    </div>
                    <i class="icon">
                        <BikeIcon />
                    </i>

                </div>

            </div>
            <div class="gap-4 flex flex-col [&_*]:text-slate-700">
                <h2 class="my-8">Licenças e Certificados</h2>
                <p>
                    Transparecemos as nossas Licenças e Certificações!
                </p>
                <p>
                    Em respeito a <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2017/lei/l13425.htm"
                        target="_blank" class="hover:underline transition-all">Lei 13.425 de 30 de março de 2017</a>,
                    publicamos aqui as
                    licenças e certificados
                    concedidos a essa empresa dando ciência de sua habilidade para o fabrico de águas envasadas. Para
                    vista dos interessados e órgãos competentes.
                </p>
                <CertificatesCarousel />
            </div>
        </div>
    </section>
</template>
