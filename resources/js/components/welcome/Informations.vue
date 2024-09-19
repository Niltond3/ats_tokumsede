<template>
    <section class="py-10 lg:py-[6.25rem] bg-grey-100">
        <div class="container lg:grid lg:grid-cols-12 lg:gap-x-10">
            <div class="mb-10 lg:col-span-5 lg:pr-[3.75rem] text-slate-700">
                <h2 class="m-0">Informações e Notícias</h2>
                <div class="mt-5 lg:mt-7">
                    <p class="p1">Aqui serão publicadas periodicamente as notícias mais recentes sobre a indústria de
                        <span class="text-[#00639A] font-bold">Águas Terra Santa</span> e seus produtos.
                    </p>
                </div>
                <div class="mt-7 lg:mt-10">
                    <a href="#contact" class="btn btn--primary">
                        Fale Conosco <i class="icon icon--chevron-right text-base"><svg
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 20">
                                <path fill="none" stroke="currentColor" vector-effect="non-scaling-stroke"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m1 19 9-9-9-9">
                                </path>
                            </svg></i> </a>
                </div>
            </div>
            <Accordion type="single" collapsible class="lg:col-span-7 flex flex-col gap-4">
                <AccordionItem v-for="item in accordionItems" :key="item.value" :value="item.value"
                    class="border-none [&>h3]:w-full flex flex-col items-start gap-4 rounded-lg bg-white shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:bg-[#1E293B] dark:ring-zinc-800">

                    <AccordionTrigger class="flex w-full gap-5 justify-center relative" as-child="true">
                        <button>
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 flex size-12 shrink-0 items-center justify-center rounded-full bg-[#0B1020]/20 sm:size-16">
                                <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <g fill="#fff">
                                        <path
                                            d="M8.75 4.5H5.5c-.69 0-1.25.56-1.25 1.25v4.75c0 .69.56 1.25 1.25 1.25h3.25c.69 0 1.25-.56 1.25-1.25V5.75c0-.69-.56-1.25-1.25-1.25Z" />
                                        <path
                                            d="M24 10a3 3 0 0 0-3-3h-2V2.5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2V20a3.5 3.5 0 0 0 3.5 3.5h17A3.5 3.5 0 0 0 24 20V10ZM3.5 21.5A1.5 1.5 0 0 1 2 20V3a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5v17c0 .295.037.588.11.874a.5.5 0 0 1-.484.625L3.5 21.5ZM22 20a1.5 1.5 0 1 1-3 0V9.5a.5.5 0 0 1 .5-.5H21a1 1 0 0 1 1 1v10Z" />
                                        <path
                                            d="M12.751 6.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 7.3v-.5a.75.75 0 0 1 .751-.753ZM12.751 10.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 11.3v-.5a.75.75 0 0 1 .751-.753ZM4.751 14.047h10a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-10A.75.75 0 0 1 4 15.3v-.5a.75.75 0 0 1 .751-.753ZM4.75 18.047h7.5a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-7.5A.75.75 0 0 1 4 19.3v-.5a.75.75 0 0 1 .75-.753Z" />
                                    </g>
                                </svg>
                            </div>
                            <h2 class=" flex flex-col text-xl font-semibold text-black dark:text-white">
                                <p class="text-xl font-semibold text-black dark:text-white">{{ item.title }}
                                </p>
                                <p class="text-sm/relaxed opacity-80">{{ item.subTitle }}</p>
                            </h2>
                        </button>
                    </AccordionTrigger>
                    <AccordionContent>
                        <div v-html=item.content>
                        </div>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </div>
    </section>
</template>

<script setup>
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
const accordionItems = [
    {
        value: 'agua-alcalina',
        title: 'O que é Água Alcalina',
        subTitle: 'É chamada alcalina toda água com pH superior a 7,5',
        content: /*html*/`
        <p>
            O termo água alcalina referece-se ao potencial hidrogeniônico ou simplemente pH da água. O nível de pH é uma medida que determina a alcalinidade ou a acidez de uma solução. Ela é medida em uma escala de 0 a 14, onde 7 é neutro, valores abaixo indicam acidez e acima representam alcalinidade. Hoje no Brasil, quem determina todos os Padrões de Potabilidade é a Portaria 2.914 do Ministério da Saúde. Essa portaria determina que o valor do pH da água destinada ao consumo humano esteja na faixa entre 6,5 e 9,5.
        </p>
        <p>
            Nossos avós tomavam água de cacimba, alcalina e cheia de minerais e por isso tinham "saúde de ferro". Hoje as principais águas encontradas no mercado apresentam níveis muito elevados de acidez, muitas delas com pH abaixo de 6.5 e chegando algumas marcas a 3.5, ou seja, extremamente acidificantes e prejudiciais. Já as águas alcalinas, que têm pH entre 7.5 e 9.5, são o alimento da vida, o hidratante que age naturalmente como preventivo dos males causados por radicais livres, reduzindo-os e neutralizando os efeitos do consumo de alimentos ácidos.
        </p>
        <p>
            O pH elevado e a presença de sais alcalinizantes como cálcio, magnésio, potássio e bicarbonatos, além de possuir propriedades digestivas baixam a tensão superficial da água, ajudam a estabilizar o fluxo intestinal e reduzem os sintomas do refluxo gastro-esofágico e da gastrite, favorecendo o bom funcionamento dos sistemas, incluindo o imunológico. Por isso e devido às suas características químicas as águas alcalinas, que são ricas em oxigênio, rejuvenescem as células e retardam o envelhecimento.
        </p>
        `
    },
    {
        value: 'porque-agua-alcalina',
        title: 'Porquê beber água alcalina enriquecida?',
        subTitle: 'Poderosa em composição',
        content: /*html*/``
    },
    {
        value: 'alkalina-leve',
        title: 'Alkalina Leve',
        subTitle: 'Pureza + Saúde + Leveza + Prazer',
        content: /*html*/`
        <p>
            A água <span class="text-[#489BFB] font-bold">Alkalina Leve</span> é um hidratante alcalino onde na sua composição foi reduzido o teor de alguns sais, como sódio e potássio, para beneficiar aqueles consumidores que possam apresentar alguma restrição alimentar aos mesmos, bem como para favorecer aqueles que valorizam o prazer de beber e os adeptos das águas mais leves, mas que não abrem mão da qualidade do que consomem.
        </p>
        <p>
            Ao contrário das águas minerais, que em sua grande maioria só tem mineral no nome, o seu conteúdo de sais é superior a quase todas as marcas existentes no mercado, o que a classifica como sendo um dos melhores líquidos para aliviar a sua sede.
        </p>
        <p>
            Por ser muito leve e por ter o pH alcalino, a água <span class="text-[#489BFB] font-bold">Alkalina Leve</span> é simultâneamente uma excelente bebida e um verdadeiro isotônico que traz inúmeros benefícios ao seu corpo.
        </p>
        `
    },
    {
        value: 'alkalina-rica',
        title: 'Alkalina Rica',
        subTitle: 'SAÚDE + JUVENTUDE + BELEZA',
        content: /*html*/`
        <p>
            A <span class="text-[#078010] font-bold">Alkalina Rica</span> funciona como um potente antioxidante, ajudando a equilibrar as condições ácidas naturais do estômago, favorecendo a digestão dos alimentos e eliminando os resíduos ácidos, produzindo uma melhoria substancial na saúde, portanto, o consumo regular de <span class="text-[#078010] font-bold">Alkalina Rica</span> proporciona ao corpo um melhor funcionamento, purificando e hidratando adequadamente.
        </p>
        <p class="text-[#078010] font-bold">
            "Uma vez que grande parte do nosso peso corporal é água, é importante considerar que tipo de água nos dá maior benefício."
        </p>
        <p>
            Quando pensamos em saúde, nos preocupamos diretamente com dieta alimentar e exercícios e nem sempre lembramos que esses só funcionam adequadamente quando aliados a uma hidratação abundante, que deve ser feita única e exclusivamente com água, saliente-se ainda que essa água tem que ser alcalina porque só ela é capaz de limpar seu interior adicionando os nutrientes direto em sua corrente sanguínea. A <span class="text-[#078010] font-bold">Alkalina Rica</span> reúne todas as condições para essa importante tarefa. Os sais minerais nela contidos exercem funções primordiais em nosso organismo, veja:
        </p>
        <p>
            1) Os <span class="text-[#078010] font-bold">Bicarbonatos</span> ativam o sistema digestivo, induzindo a formação de mais suco gástrico que propicia uma melhor digestão e ao mesmo tempo produz mais bicarbonatos que alcalinizam o seu sangue. Nesse processo há a queima de gorduras ácidas acumuladas no corpo e conseqüentemente ocorre a perda de peso.
        </p>
        <p>
            2) O <span class="text-[#078010] font-bold">Potássio</span> elimina toxinas e líquidos excedentes baixando a pressão arterial, ativando o sistema cardíaco e muscular e favorecendo o ganho de massa magra.
        </p>
        <p>
            3) O <span class="text-[#078010] font-bold">Cálcio</span> fortalece a massa óssea e recupera as cartilagens desgastadas durante o esforço físico diário.
        </p>
        <p>
            4) O <span class="text-[#078010] font-bold">Magnésio</span> é o responsável pelo controle enzimático de mais de 360 substâncias no nosso organismo, regulando a entrada e saída de todos esses elementos e restabelecendo o equilíbrio funcional do nosso corpo.
        </p>
        <p>
            A composição balanceada de minerais essenciais da <span class="text-[#078010] font-bold">Alkalina Rica</span> é, portanto, o grande diferencial na dieta daqueles que têm sede de saúde, das gerações saudáveis que buscam o equilíbrio entre corpo e mente sãs.
        </p>
        `
    },
    {
        value: 'alkalina-sport',
        title: 'Alkalina Sport',
        subTitle: 'ELETRÓLITOS + ANTIOXIDANTES + ENERGIA',
        content: `A Água Alcalina Tope, com pH 9.0 é um hidratante elaborado especialmente para o atleta, para o desportista, aquele que tem um trabalho extenuante e pra você que faz academia com regularidade. Sua formulação é composta por minerais de cálcio, magnésio e principalmente potássio que atua diretamente nos momentos de maior esforço físico, potencializando sua resistência ao desgaste natural dessa ação.
        É no esporte, no trabalho pesado e na academia onde os músculos mais queimam energia. É no momento desse esforço que seu corpo mais precisa desses minerais para um melhor rendimento.
        1) O Cálcio O cálcio é quem te dá firmeza estrutural, na formação e renovação de toda sua massa óssea e articular.

        2) O Magnésio te dá precisão em todas as reações metabólicas que seu corpo exige para cada movimento.

        3) O Potássio ativa a função cárdio-vascular, neuro-muscular, levando a energia necessária para cada reação.`
    },
]
</script>
