<script setup>
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion';

const defaultValue = 'alkalina-leve';

const accordionItems = [
  {
    value: 'alkalina-leve',
    title: 'Alkalina Leve',
    subTitle: 'Pureza + Saúde + Leveza + Prazer',
    content: html`
      <p>
        A água <span class="text-[#489BFB] font-bold">Alkalina Leve</span> é um hidratante alcalino
        onde na sua composição foi reduzido o teor de alguns sais, como sódio e potássio, para
        beneficiar aqueles consumidores que possam apresentar alguma restrição alimentar aos mesmos,
        bem como para favorecer aqueles que valorizam o prazer de beber e os adeptos das águas mais
        leves, mas que não abrem mão da qualidade do que consomem.
      </p>
      <p>
        Ao contrário das águas minerais, que em sua grande maioria só tem mineral no nome, o seu
        conteúdo de sais é superior a quase todas as marcas existentes no mercado, o que a
        classifica como sendo um dos melhores líquidos para aliviar a sua sede.
      </p>
      <p>
        Por ser muito leve e por ter o pH alcalino, a água
        <span class="text-[#489BFB] font-bold">Alkalina Leve</span> é simultâneamente uma excelente
        bebida e um verdadeiro isotônico que traz inúmeros benefícios ao seu corpo.
      </p>
    `,
  },
  {
    value: 'alkalina-rica',
    title: 'Alkalina Rica',
    subTitle: 'SAÚDE + JUVENTUDE + BELEZA',
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
    subTitle: 'ELETRÓLITOS + ANTIOXIDANTES + ENERGIA',
    content: `A Água Alcalina Tope, com pH 9.0 é um hidratante elaborado especialmente para o atleta, para o desportista, aquele que tem um trabalho extenuante e pra você que faz academia com regularidade. Sua formulação é composta por minerais de cálcio, magnésio e principalmente potássio que atua diretamente nos momentos de maior esforço físico, potencializando sua resistência ao desgaste natural dessa ação.
        É no esporte, no trabalho pesado e na academia onde os músculos mais queimam energia. É no momento desse esforço que seu corpo mais precisa desses minerais para um melhor rendimento.
        1) O Cálcio O cálcio é quem te dá firmeza estrutural, na formação e renovação de toda sua massa óssea e articular.

        2) O Magnésio te dá precisão em todas as reações metabólicas que seu corpo exige para cada movimento.

        3) O Potássio ativa a função cárdio-vascular, neuro-muscular, levando a energia necessária para cada reação.`,
  },
];
</script>

<template>
  <section
    class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#0B1020] selection:text-white"
  >
    <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
      <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
        <nav class="flex lg:justify-center lg:col-start-2">
          <a
            href="#aguaalcalina"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#0B1020] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
          >
            Água Alcalina?
          </a>
          <a
            href="#puraeleve"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#0B1020] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
          >
            Alkalina Leve
          </a>
          <a
            href="#aguarica"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#0B1020] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
          >
            Alkalina Rica
          </a>
          <a
            href="#alcalinatope"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#0B1020] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
          >
            Alkalina Sport
          </a>
        </nav>
        <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
          <Link
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#0B1020] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
          >
            Dashboard
          </Link>

          <template v-else>
            <Link
              :href="route('login')"
              class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#0B1020] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
              Log in
            </Link>

            <Link
              v-if="canRegister"
              :href="route('register')"
              class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#0B1020] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
              Register
            </Link>
          </template>
        </nav>
      </header>

      <div class="mt-6">
        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
          <a
            id="aguaalcalina"
            href="https://laravel.com/docs"
            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#0B1020] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-[#1E293B] dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#0B1020]"
          >
            <div id="screenshot-container" class="relative flex w-full flex-1 items-stretch">
              <img
                src="/images/escala.png"
                alt="escalaPh"
                class="aspect-video h-[67.6%] w-full flex-1 rounded-[10px] object-top object-cover drop-shadow-[0px_4px_34px_rgba(0,0,0,0.06)] dark:hidden"
                @error="handleImageError"
              />
              <img
                src="/images/escala.png"
                alt="escalaPh"
                class="hidden aspect-video h-[67.6%] w-full flex-1 rounded-[10px] object-top object-cover drop-shadow-[0px_4px_34px_rgba(0,0,0,0.25)] dark:block"
              />
              <div
                class="absolute -bottom-16 -left-16 h-40 w-[calc(100%+8rem)] bg-gradient-to-b from-transparent via-white to-white dark:via-zinc-900 dark:to-zinc-900"
              ></div>
            </div>

            <div class="relative flex items-center gap-6 lg:items-end">
              <div id="docs-card-content" class="flex items-start gap-6 lg:flex-col">
                <div
                  class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#0B1020]/20 sm:size-16"
                >
                  <svg
                    class="size-5 sm:size-6"
                    xmlns="https://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <path
                      fill="#fff"
                      d="M23 4a1 1 0 0 0-1.447-.894L12.224 7.77a.5.5 0 0 1-.448 0L2.447 3.106A1 1 0 0 0 1 4v13.382a1.99 1.99 0 0 0 1.105 1.79l9.448 4.728c.14.065.293.1.447.1.154-.005.306-.04.447-.105l9.453-4.724a1.99 1.99 0 0 0 1.1-1.789V4ZM3 6.023a.25.25 0 0 1 .362-.223l7.5 3.75a.251.251 0 0 1 .138.223v11.2a.25.25 0 0 1-.362.224l-7.5-3.75a.25.25 0 0 1-.138-.22V6.023Zm18 11.2a.25.25 0 0 1-.138.224l-7.5 3.75a.249.249 0 0 1-.329-.099.249.249 0 0 1-.033-.12V9.772a.251.251 0 0 1 .138-.224l7.5-3.75a.25.25 0 0 1 .362.224v11.2Z"
                    />
                    <path
                      fill="#fff"
                      d="m3.55 1.893 8 4.048a1.008 1.008 0 0 0 .9 0l8-4.048a1 1 0 0 0-.9-1.785l-7.322 3.706a.506.506 0 0 1-.452 0L4.454.108a1 1 0 0 0-.9 1.785H3.55Z"
                    />
                  </svg>
                </div>

                <div class="pt-3 sm:pt-5 lg:pt-0">
                  <h2 class="text-xl font-semibold text-black dark:text-white">
                    O que é água Alcalina
                  </h2>
                  <p class="mt-6 text-sm/relaxed opacity-80">
                    É chamada alcalina toda água com pH superior a 7,5
                  </p>

                  <p class="mt-4 text-sm/relaxed">
                    O termo <span>água alcalina</span> referece-se ao
                    <span>potencial hidrogeniônico</span> ou simplemente <span>pH da água</span>. O
                    nível de pH é uma medida que determina a alcalinidade ou a acidez de uma
                    solução. Ela é medida em uma escala de 0 a 14, onde 7 é neutro, valores abaixo
                    indicam acidez e acima representam alcalinidade. Hoje no Brasil, quem determina
                    todos os Padrões de Potabilidade é a
                    <a
                      class="link"
                      href="https://bvsms.saude.gov.br/bvs/saudelegis/gm/2011/prt2914_12_12_2011.html"
                      target="_blank"
                      >Portaria 2.914</a
                    >
                    do Ministério da Saúde. Essa portaria determina que o valor do pH da água
                    destinada ao consumo humano esteja na faixa entre 6,5 e 9,5.
                  </p>
                  <p>
                    Nossos avós tomavam água de cacimba, alcalina e cheia de minerais e por isso
                    tinham "saúde de ferro". Hoje as principais águas encontradas no mercado
                    apresentam níveis muito elevados de acidez, muitas delas
                    <span>com pH abaixo de 6.5 e chegando algumas marcas a 3.5</span>, ou seja,
                    extremamente acidificantes e prejudiciais. Já as <span>águas alcalinas</span>,
                    que têm <span>pH entre 7.5 e 9.5</span>, são o alimento da vida, o hidratante
                    que age naturalmente como preventivo dos males causados por radicais livres,
                    reduzindo-os e neutralizando os efeitos do consumo de alimentos ácidos.
                  </p>
                </div>
              </div>

              <svg
                class="size-6 shrink-0 stroke-[#0B1020]"
                xmlns="https://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                />
              </svg>
            </div>
          </a>
          <Accordion type="single" collapsible :default-value="defaultValue">
            <AccordionItem
              v-for="item in accordionItems"
              :key="item.value"
              :value="item.value"
              class="[&>h3]:w-full flex flex-col items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-[#1E293B] dark:ring-zinc-800"
            >
              <AccordionTrigger class="flex w-full gap-5 justify-center relative" as-child="true">
                <button>
                  <div
                    class="absolute left-0 top-1/2 -translate-y-1/2 flex size-12 shrink-0 items-center justify-center rounded-full bg-[#0B1020]/20 sm:size-16"
                  >
                    <svg
                      class="size-5 sm:size-6"
                      xmlns="https://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <g fill="#fff">
                        <path
                          d="M8.75 4.5H5.5c-.69 0-1.25.56-1.25 1.25v4.75c0 .69.56 1.25 1.25 1.25h3.25c.69 0 1.25-.56 1.25-1.25V5.75c0-.69-.56-1.25-1.25-1.25Z"
                        />
                        <path
                          d="M24 10a3 3 0 0 0-3-3h-2V2.5a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2V20a3.5 3.5 0 0 0 3.5 3.5h17A3.5 3.5 0 0 0 24 20V10ZM3.5 21.5A1.5 1.5 0 0 1 2 20V3a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5v17c0 .295.037.588.11.874a.5.5 0 0 1-.484.625L3.5 21.5ZM22 20a1.5 1.5 0 1 1-3 0V9.5a.5.5 0 0 1 .5-.5H21a1 1 0 0 1 1 1v10Z"
                        />
                        <path
                          d="M12.751 6.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 7.3v-.5a.75.75 0 0 1 .751-.753ZM12.751 10.047h2a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-2A.75.75 0 0 1 12 11.3v-.5a.75.75 0 0 1 .751-.753ZM4.751 14.047h10a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-10A.75.75 0 0 1 4 15.3v-.5a.75.75 0 0 1 .751-.753ZM4.75 18.047h7.5a.75.75 0 0 1 .75.75v.5a.75.75 0 0 1-.75.75h-7.5A.75.75 0 0 1 4 19.3v-.5a.75.75 0 0 1 .75-.753Z"
                        />
                      </g>
                    </svg>
                  </div>
                  <h2 class="flex flex-col text-xl font-semibold text-black dark:text-white">
                    <p class="text-xl font-semibold text-black dark:text-white">{{ item.title }}</p>
                    <p class="text-sm/relaxed opacity-80">{{ item.subTitle }}</p>
                  </h2>
                </button>
              </AccordionTrigger>
              <AccordionContent>
                <div v-html="item.content"></div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>

          <!-- <a href="https://laracasts.com" id="alkalina-leve"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#0B1020] lg:pb-10 dark:bg-[#1E293B] dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#0B1020]">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#0B1020]/20 sm:size-16">
                                <svg class="size-5 sm:size-6" xmlns="https://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <g fill="#fff">
                                        <path
                                            d="M24 8.25a.5.5 0 0 0-.5-.5H.5a.5.5 0 0 0-.5.5v12a2.5 2.5 0 0 0 2.5 2.5h19a2.5 2.5 0 0 0 2.5-2.5v-12Zm-7.765 5.868a1.221 1.221 0 0 1 0 2.264l-6.626 2.776A1.153 1.153 0 0 1 8 18.123v-5.746a1.151 1.151 0 0 1 1.609-1.035l6.626 2.776ZM19.564 1.677a.25.25 0 0 0-.177-.427H15.6a.106.106 0 0 0-.072.03l-4.54 4.543a.25.25 0 0 0 .177.427h3.783c.027 0 .054-.01.073-.03l4.543-4.543ZM22.071 1.318a.047.047 0 0 0-.045.013l-4.492 4.492a.249.249 0 0 0 .038.385.25.25 0 0 0 .14.042h5.784a.5.5 0 0 0 .5-.5v-2a2.5 2.5 0 0 0-1.925-2.432ZM13.014 1.677a.25.25 0 0 0-.178-.427H9.101a.106.106 0 0 0-.073.03l-4.54 4.543a.25.25 0 0 0 .177.427H8.4a.106.106 0 0 0 .073-.03l4.54-4.543ZM6.513 1.677a.25.25 0 0 0-.177-.427H2.5A2.5 2.5 0 0 0 0 3.75v2a.5.5 0 0 0 .5.5h1.4a.106.106 0 0 0 .073-.03l4.54-4.543Z" />
                                    </g>
                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">Alkalina Leve</h2>
                                <h3 class="text-[#489BFB]">PUREZA + SAÚDE + LEVEZA + PRAZER</h3>
                                <p class="mt-4 text-sm/relaxed">
                                <p> A água <span class="text-[#489BFB]">Alkalina Leve</span> é um hidratante alcalino
                                    onde na sua composição foi reduzido o teor de alguns sais, como sódio e potássio,
                                    para beneficiar
                                    aqueles consumidores que possam apresentar alguma restrição alimentar aos mesmos,
                                    bem como para
                                    favorecer aqueles que valorizam o prazer de beber e os adeptos das águas mais leves,
                                    mas que não
                                    abrem mão da qualidade do que consomem.</p>
                                <p>Ao contrário das águas minerais, que em sua grande maioria só tem mineral no nome,
                                    o seu conteúdo de sais é superior a quase todas as marcas existentes no mercado, o
                                    que a classifica como sendo um dos melhores líquidos para aliviar a sua sede.</p>
                                <p>Por ser muito leve e por ter o pH alcalino, a água <span
                                        class="text-[#489BFB]">Alkalina Leve</span>
                                    é simultâneamente uma excelente bebida e um verdadeiro isotônico que traz inúmeros
                                    benefícios ao seu
                                    corpo.</p>
                                </p>
                            </div>

                            <svg class="size-6 shrink-0 self-center stroke-[#0B1020]" xmlns="https://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </a>

                        <a href="https://laravel-news.com" id="alkalina-rica"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#0B1020] lg:pb-10 dark:bg-[#1E293B] dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#0B1020]">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#0B1020]/20 sm:size-16">
                                <svg class="size-5 sm:size-6" xmlns="https://www.w3.org/2000/svg" fill="none"
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

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">Alkalina Rica</h2>
                                <h3 class="text-[#078010]">+ SAÚDE + JUVENTUDE + BELEZA</h3>
                                <p class="mt-4 text-sm/relaxed">
                                    A <span className='text-[#078010]'>ÁguaRICA</span> funciona como um potente
                                    antioxidante, ajudando a
                                    equilibrar
                                    as condições ácidas naturais do estômago, favorecendo a digestão
                                    dos alimentos e eliminando os resíduos ácidos, produzindo uma
                                    melhoria substancial na saúde, portanto, o consumo regular de
                                    <span style="color: #078010">ÁguaRICA</span> proporciona ao corpo um melhor
                                    funcionamento,
                                    purificando e hidratando adequadamente.
                                </p>
                                <p><span style="color: #078010">"Uma vez que grande parte do nosso peso corporal é água,
                                        é
                                        importante considerar que tipo de água nos dá maior benefício."</span><br></p>
                                <p>Quando pensamos em saúde, nos preocupamos diretamente com dieta alimentar e
                                    exercícios e nem
                                    sempre lembramos que esses só funcionam adequadamente quando aliados a uma
                                    hidratação abundante,
                                    que deve ser feita única e exclusivamente com água, saliente-se ainda que essa água
                                    tem que ser
                                    alcalina porque só ela é capaz de
                                    limpar seu interior adicionando os nutrientes direto em sua corrente sanguínea. A
                                    <span style="color: #078010">ÁguaRICA</span> reúne
                                    todas as condições para essa importante tarefa. Os sais minerais nela contidos
                                    exercem
                                    funções primordiais em nosso organismo, veja:
                                </p>
                            </div>

                            <svg class="size-6 shrink-0 self-center stroke-[#0B1020]" xmlns="https://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                            </svg>
                        </a>

                        <div id="alkalina-sport"
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-[#1E293B] dark:ring-zinc-800">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#0B1020]/20 sm:size-16">
                                <svg class="size-5 sm:size-6" xmlns="https://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <g fill="#fff">
                                        <path
                                            d="M16.597 12.635a.247.247 0 0 0-.08-.237 2.234 2.234 0 0 1-.769-1.68c.001-.195.03-.39.084-.578a.25.25 0 0 0-.09-.267 8.8 8.8 0 0 0-4.826-1.66.25.25 0 0 0-.268.181 2.5 2.5 0 0 1-2.4 1.824.045.045 0 0 0-.045.037 12.255 12.255 0 0 0-.093 3.86.251.251 0 0 0 .208.214c2.22.366 4.367 1.08 6.362 2.118a.252.252 0 0 0 .32-.079 10.09 10.09 0 0 0 1.597-3.733ZM13.616 17.968a.25.25 0 0 0-.063-.407A19.697 19.697 0 0 0 8.91 15.98a.25.25 0 0 0-.287.325c.151.455.334.898.548 1.328.437.827.981 1.594 1.619 2.28a.249.249 0 0 0 .32.044 29.13 29.13 0 0 0 2.506-1.99ZM6.303 14.105a.25.25 0 0 0 .265-.274 13.048 13.048 0 0 1 .205-4.045.062.062 0 0 0-.022-.07 2.5 2.5 0 0 1-.777-.982.25.25 0 0 0-.271-.149 11 11 0 0 0-5.6 2.815.255.255 0 0 0-.075.163c-.008.135-.02.27-.02.406.002.8.084 1.598.246 2.381a.25.25 0 0 0 .303.193 19.924 19.924 0 0 1 5.746-.438ZM9.228 20.914a.25.25 0 0 0 .1-.393 11.53 11.53 0 0 1-1.5-2.22 12.238 12.238 0 0 1-.91-2.465.248.248 0 0 0-.22-.187 18.876 18.876 0 0 0-5.69.33.249.249 0 0 0-.179.336c.838 2.142 2.272 4 4.132 5.353a.254.254 0 0 0 .15.048c1.41-.01 2.807-.282 4.117-.802ZM18.93 12.957l-.005-.008a.25.25 0 0 0-.268-.082 2.21 2.21 0 0 1-.41.081.25.25 0 0 0-.217.2c-.582 2.66-2.127 5.35-5.75 7.843a.248.248 0 0 0-.09.299.25.25 0 0 0 .065.091 28.703 28.703 0 0 0 2.662 2.12.246.246 0 0 0 .209.037c2.579-.701 4.85-2.242 6.456-4.378a.25.25 0 0 0 .048-.189 13.51 13.51 0 0 0-2.7-6.014ZM5.702 7.058a.254.254 0 0 0 .2-.165A2.488 2.488 0 0 1 7.98 5.245a.093.093 0 0 0 .078-.062 19.734 19.734 0 0 1 3.055-4.74.25.25 0 0 0-.21-.41 12.009 12.009 0 0 0-10.4 8.558.25.25 0 0 0 .373.281 12.912 12.912 0 0 1 4.826-1.814ZM10.773 22.052a.25.25 0 0 0-.28-.046c-.758.356-1.55.635-2.365.833a.25.25 0 0 0-.022.48c1.252.43 2.568.65 3.893.65.1 0 .2 0 .3-.008a.25.25 0 0 0 .147-.444c-.526-.424-1.1-.917-1.673-1.465ZM18.744 8.436a.249.249 0 0 0 .15.228 2.246 2.246 0 0 1 1.352 2.054c0 .337-.08.67-.23.972a.25.25 0 0 0 .042.28l.007.009a15.016 15.016 0 0 1 2.52 4.6.25.25 0 0 0 .37.132.25.25 0 0 0 .096-.114c.623-1.464.944-3.039.945-4.63a12.005 12.005 0 0 0-5.78-10.258.25.25 0 0 0-.373.274c.547 2.109.85 4.274.901 6.453ZM9.61 5.38a.25.25 0 0 0 .08.31c.34.24.616.561.8.935a.25.25 0 0 0 .3.127.631.631 0 0 1 .206-.034c2.054.078 4.036.772 5.69 1.991a.251.251 0 0 0 .267.024c.046-.024.093-.047.141-.067a.25.25 0 0 0 .151-.23A29.98 29.98 0 0 0 15.957.764a.25.25 0 0 0-.16-.164 11.924 11.924 0 0 0-2.21-.518.252.252 0 0 0-.215.076A22.456 22.456 0 0 0 9.61 5.38Z" />
                                    </g>
                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">Alkalina Sport</h2>
                                <h3 class="text-[#046A7C]">+ ELETRÓLITOS + ANTIOXIDANTES + ENERGIA</h3>

                                <p class="mt-4 text-sm/relaxed">
                                    A <span style="color: #046A7C">Alkalina Sport</span>, com <span
                                        style="color: #046A7C">pH
                                        9.0</span> é um hidratante elaborado
                                    especialmente para o atleta, para o desportista, aquele que tem um trabalho
                                    extenuante
                                    e pra você que faz academia com regularidade. Sua formulação é composta por minerais
                                    de cálcio, magnésio e principalmente potássio que atua diretamente nos momentos de
                                </p>
                            </div>
                        </div> -->
        </div>
      </div>
    </div>
  </section>
</template>
