<template>
  <section id="informations" class="py-10 lg:py-[6.25rem] bg-grey-100">
    <div class="container lg:grid lg:grid-cols-12 lg:gap-x-10">
      <div class="mb-10 lg:col-span-5 lg:pr-[3.75rem] text-slate-700">
        <h2 class="m-0">Informações e Notícias</h2>
        <div class="mt-5 lg:mt-7">
          <p class="p1">
            Aqui serão publicadas periodicamente as notícias mais recentes sobre a indústria de
            <span class="text-[#00639A] font-bold">Águas Terra Santa</span> e seus produtos.
          </p>
        </div>
        <div class="mt-7 lg:mt-10">
          <Button href="#support" class="btn--primary">
            Saiba Mais
            <i class="icon icon--chevron-right text-xl">
              <ArrowRightIcon />
            </i>
          </Button>
        </div>
      </div>
      <Accordion type="single" collapsible class="lg:col-span-7 flex flex-col gap-4">
        <AccordionItem
          v-for="item in accordionItems"
          :key="item.value"
          :value="item.value"
          class="border-none [&>h3]:w-full flex flex-col items-start gap-4 rounded-lg bg-white shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] dark:bg-[#1E293B] dark:ring-zinc-800 [&_.indicator]:data-[state=open]:before:rotate-90"
        >
          <AccordionTrigger
            class="group w-full gap-5 relative hover:no-underline flex gap-x-3 font-bold text-blue-900 py-4 px-5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden lg:py-5 lg:px-7 lg:text-xl lg:leading-8 shadow sm:rounded-lg dark:shadow-dark accordion-container-inside"
          >
            <div
              class="indicator relative w-8 h-8 rounded-full bg-blue-100 text-blue shrink-0 before:absolute before:left-1/2 before:top-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:h-4 before:w-[0.125rem] before:bg-current before:rounded-full before:transition-transform after:absolute after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:w-4 after:h-[0.125rem] after:bg-current after:rounded-full group-hover:bg-blue-900 group-hover:text-white transition-colors group-focus:bg-blue-900 group-focus:text-white lg:w-10 lg:h-10 after:lg:w-5 before:lg:h-5"
            ></div>
            <h2 class="flex flex-col text-lg font-semibold text-blue-950 dark:text-white">
              <p class="text-xl font-semibold dark:text-white">{{ item.title }}</p>
              <p class="text-sm/relaxed opacity-80">{{ item.subTitle }}</p>
            </h2>
          </AccordionTrigger>
          <AccordionContent>
            <div
              class="pb-4 px-5 dark:text-grey-700 lg:pb-5 lg:px-7 indent-3.5 flex flex-col gap-3 text-slate-900 [&_p]:text-slate-700"
              v-html="item.content"
            ></div>
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>
  </section>
</template>

<script setup>
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion';
import { RiArrowRightWideLine as ArrowRightIcon } from 'vue-remix-icons';

import Button from '@/components/Button.vue';

// import { RiNewspaperFill } from "oh-vue-icons/icons/ri";

const accordionItems = [
  {
    value: 'agua-alcalina',
    title: 'Água Alcalina?',
    subTitle: 'É chamada alcalina toda água com pH superior a 7,5',
    content: /*html*/ `
        <p>
            O termo <span class="text-blue-800 font-semibold">água alcalina</span> referece-se ao <span class="text-blue-800 font-semibold">potencial hidrogeniônico</span> ou simplemente <span class="text-blue-800 font-semibold">pH da água</span>. O nível de pH é uma medida que determina a alcalinidade ou a acidez de uma solução. Ela é medida em uma escala de 0 a 14, onde 7 é neutro, valores abaixo indicam acidez e acima representam alcalinidade. Hoje no Brasil, quem determina todos os Padrões de Potabilidade é a <a class="text-blue-800 font-semibold underline" href="https://bvsms.saude.gov.br/bvs/saudelegis/gm/2011/prt2914_12_12_2011.html" target="_blank">Portaria 2.914</a> do Ministério da Saúde. Essa portaria determina que o valor do pH da água destinada ao consumo humano esteja na faixa entre 6,5 e 9,5.
        </p>
        <p>
            Nossos avós tomavam água de cacimba, alcalina e cheia de minerais e por isso tinham "saúde de ferro". Hoje as principais águas encontradas no mercado apresentam níveis muito elevados de acidez, muitas delas <span class="text-blue-800 font-semibold">com pH abaixo de 6.5 e chegando algumas marcas a 3.5</span>, ou seja, extremamente acidificantes e prejudiciais. Já as <span class="text-blue-800 font-semibold">água alcalina</span>, que têm <span class="text-blue-800 font-semibold">pH entre 7.5 e 9.5</span>, são o alimento da vida, o hidratante que age naturalmente como preventivo dos males causados por radicais livres, reduzindo-os e neutralizando os efeitos do consumo de alimentos ácidos.
        </p>
        <p>
            O <span class="text-blue-800 font-semibold">pH da água</span> e a <span class="text-blue-800 font-semibold">presença de sais alcalinizantes</span> como cálcio, magnésio, potássio e bicarbonatos, além de possuir propriedades digestivas baixam a tensão superficial da água, ajudam a estabilizar o fluxo intestinal e reduzem os sintomas do refluxo gastro-esofágico e da gastrite, favorecendo o bom funcionamento dos sistemas, incluindo o imunológico. Por isso e devido às suas características químicas as águas alcalinas, que são ricas em oxigênio, rejuvenescem as células e retardam o envelhecimento.
        </p>
        `,
  },
  {
    value: 'porque-agua-alcalina',
    title: 'Porquê consumir?',
    subTitle: 'Poderosa em composição',
    content: /*html*/ `
        <p class="w-full text-center text-blue-800 font-semibold">Por atender a todos os requisitos de uma boa água</p>
        <ul class="[&>li]:opacity-80 cursor-default [&_p]:text-blue-800 [&_p]:font-semibold flex flex-col gap-4 ">
            <li class="hover:opacity-100">
                <p>Alcalinidade</p>
                Com pH entre 8 e 10, facilita a condução dos nutrientes na circulação sanguínea, a eliminação de ácidos e o equilibrio dos radicais livres.
            </li>
            <li class="hover:opacity-100">
                <p>Pureza</p>
                É submetida aos mais eficientes e inovadores processos de tratamento, sanitização e assepsia que garantem e certificam a sua pureza.
            </li>
            <li class="hover:opacity-100">
                <p>Boa condutividade</p>
                A presença de minerais dissolvidos eleva a sua condutividade elétrica, sua alcalinidade e ionização, aumentando a concentração de oxigênio nas células.
            </li>
            <li class="hover:opacity-100">
                <p>Capacidade antioxidante</p>
                O seu redox ou ORP negativo lhe dá a capacidade de ser antioxidante, ou seja, o poder de combater agentes nocivos e toxinas que tem ions positivos.
            </li>
            <li class="hover:opacity-100">
                <p>Baixa tensão superficial</p>
                Isso garante o seu grande poder de hidratação, por formar pequenos clusters (grupos de poucas moléculas) que facilitam a sua entrada em pequenos capilares, ou seja, facilita a nutrição de órgãos como pele e cabelo melhorando seu aspecto de vitalidade.
            </li>
        </ul>
        `,
  },
  {
    value: 'alkalina-leve',
    title: 'Alkalina Leve',
    subTitle: '+ Saúde + Leveza + Pureza',
    content: /*html*/ `
        <p>
            A água <span class="text-[#489BFB] font-bold">Alkalina Leve</span> é um hidratante alcalino onde na sua composição foi reduzido o teor de potássio, e zerado o sodio, para beneficiar aqueles consumidores que possam apresentar alguma restrição alimentar, bem como para favorecer aqueles que valorizam o prazer de beber e os adeptos das águas mais leves, mas que não abrem mão da qualidade do que consomem.
        </p>
        <p>
            Ao contrário das águas minerais, que em sua grande maioria só tem mineral no nome, o seu conteúdo de sais é superior a quase todas as marcas existentes no mercado, o que a classifica como sendo um dos melhores líquidos para aliviar a sua sede.
        </p>
        <p>
            Por ser muito leve e por ter o pH alcalino, a água <span class="text-[#489BFB] font-bold">Alkalina Leve</span> é simultâneamente uma excelente bebida e um verdadeiro isotônico que traz inúmeros benefícios ao seu corpo.
        </p>
        <p>
            COMPOSIÇÃO
        </p>
        `,
  },
  {
    value: 'alkalina-rica',
    title: 'Alkalina Rica',
    subTitle: '+ Saúde + Juventude + Beleza',
    content: /*html*/ `
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
        `,
  },
  {
    value: 'alkalina-sport',
    title: 'Alkalina Sport',
    subTitle: '+ Eletrólitos + Antioxidantes + Energia',
    content: `A Água Alcalina Sport, com pH 10.0 é um hidratante elaborado especialmente para o atleta, para o desportista, aquele que tem um trabalho extenuante e pra você que faz academia com regularidade. Sua formulação é composta por minerais de cálcio, magnésio e principalmente potássio que atua diretamente nos momentos de maior esforço físico, potencializando sua resistência ao desgaste natural dessa ação.
        É no esporte, no trabalho pesado e na academia onde os músculos mais queimam energia. É no momento desse esforço que seu corpo mais precisa desses minerais para um melhor rendimento.

        1) O Cálcio O cálcio é quem te dá firmeza estrutural, na formação e renovação de toda sua massa óssea e articular.

        2) O Magnésio te dá precisão em todas as reações metabólicas que seu corpo exige para cada movimento.

        3) O Potássio ativa a função cárdio-vascular, neuro-muscular, levando a energia necessária para cada reação.


        `,
  },
];
</script>
