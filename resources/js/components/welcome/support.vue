<script setup>
import Button from '../Button.vue';
import { RiWhatsappLine as WhatsappIcon } from "vue-remix-icons";

</script>

<template>
    <section class="container flex [&>*]:flex-1 gap-4 xl:px-64 mt-20 py-10" id="support">
        <div class="flex gap-7 flex-col items-center">
            <img src="/images/saiba_mais.png" class="w-min" />
            <div class="gap-4 flex flex-col text-slate-900">
                <h3>Como Comprar?</h3>
                <div class="flex">
                    <div >
                        <p>Passo1</p>
                        <p>Escolha a forma que mais lhe agrada para realizar o pedido</p>
                    </div>
                    <Button>
                        whatspp
                        <i class="icon icon--chevron-right text-xl">
                            <WhatsappIcon></WhatsappIcon>
                        </i>
                    </Button>
                    <Button>site</Button>
                    <Button>aplicativo</Button>
                </div>
                <div>
                    <div>
                        <p>Passo2</p>
                        <p>Selecione a água Alkalina que melhor atende suas necessidades, e clique em Comprar</p>
                    </div>
                    <p>shop icon</p>
                </div>
                <div>
                    <div>
                        <p>Passo3</p>
                        <p>Confira seu pedido e clique em Finalizar</p>
                    </div>
                    <p>cart icon</p>
                </div>
                <div>
                    <div>
                        <p>Passo4</p>
                        <p>Se ainda não tiver feito, faça seu cadastro! É Rápido </p>
                    </div>
                    <p>print da tela de cadastro</p>
                </div>
                <div>
                    <div>
                        <p>Passo5</p>
                        <p>Selecione a forma de pagamento, Finalize a Compra. Agora é só esperar</p>
                    </div>
                    <p>Bike Icon</p>
                </div>
            </div>
            <div class="gap-4 flex flex-col text-slate-900">
                <h3>Licenças e Certificados</h3>
                <p>
                    Transparecemos as nossas Licenças e Certificações!
                </p>
                <p>
                    Em respeito a <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2017/lei/l13425.htm"
                        target="_blank" class="underline">Lei 13.425 de 30 de março de 2017</a>, publicamos aqui as
                    licenças e certificados
                    concedidos a essa empresa dando ciência de sua habilidade para o fabrico de águas envasadas. Para
                    vista dos interessados e órgãos competentes.
                </p>
            </div>
        </div>
    </section>
</template>
