<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';

const isPrivacyPolicyVisible = ref(false);
const sectionRef = ref(null);

const checkHash = () => {
  const shouldBeVisible = window.location.hash === '#privacidade';
  if (isPrivacyPolicyVisible.value !== shouldBeVisible) {
    isPrivacyPolicyVisible.value = shouldBeVisible;

    if (shouldBeVisible && sectionRef.value) {
      nextTick(() => {
        sectionRef.value.scrollIntoView({ behavior: 'auto', block: 'start' });
      });
    }
  }
};

const handlePrivacyLinkClick = (event) => {
  event.preventDefault();

  if (isPrivacyPolicyVisible.value) {
    const currentScrollY = window.scrollY;

    isPrivacyPolicyVisible.value = false;

    nextTick(() => {
      window.scrollTo({ top: currentScrollY, behavior: 'auto' });
    });
  } else {
    isPrivacyPolicyVisible.value = true;

    if (window.location.hash !== '#privacidade') {
      history.replaceState(null, '', '#privacidade');
    }

    nextTick(() => {
      if (sectionRef.value) {
        sectionRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }
};

onMounted(() => {
  checkHash();
  window.addEventListener('hashchange', checkHash);
});

onUnmounted(() => {
  window.removeEventListener('hashchange', checkHash);
});
</script>

<template>
  <!-- Adicione a ref ao elemento section -->
  <section
    id="privacidade"
    ref="sectionRef"
    class="container mx-auto px-4 py-8 md:px-[10%] md:py-10"
  >
    <h1 class="text-center mb-8">
      <a href="#privacidade" class="font-medium hover:underline" @click="handlePrivacyLinkClick">
        Política de Privacidade
      </a>
    </h1>

    <div
      class="space-y-6 overflow-hidden transition-all duration-700 ease-in-out"
      :class="{
        'max-h-0': !isPrivacyPolicyVisible,
        'max-h-[5000px]': isPrivacyPolicyVisible,
      }"
    >
      <!-- ... (conteúdo interno inalterado) ... -->
      <!-- Introduction -->
      <p class="text-justify indent-8">
        A sua privacidade é muito importante para o TôKumSede. Dessa forma, desenvolvemos uma
        Política de Privacidade que dispõe sobre a maneira como nós obtemos, usamos e armazenamos
        suas informações. Por favor, leia com atenção o texto abaixo e fique à vontade para
        contatar-nos em caso de dúvidas.
      </p>

      <!-- Data Collection -->
      <p class="text-justify indent-8">
        Entre os tipos de dados pessoais que este aplicativo pode recolher estão: e-mail, nome,
        gênero, número de telefone, informações de cartão de crédito, cookies, endereço de
        correspondência e dados de uso. Esses dados podem ser livremente fornecidos pelo usuário, ou
        coletados automaticamente quando se utiliza este aplicativo.
      </p>
      <p class="text-justify indent-8">
        Qualquer uso de cookies, ou de outras ferramentas de rastreamento, servem para identificar
        os usuários e lembrar as suas preferências, com o único propósito de fornecer os serviços
        requeridos por eles.
      </p>
      <p class="text-justify indent-8">
        O não fornecimento de determinados dados pessoais pode tornar impossível para este
        aplicativo prestar os seus serviços.
      </p>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Modo de Processamento dos Dados</h3>
        <p class="text-justify indent-8">
          O controlador de dados processa os dados de usuários de forma adequada e tomará as medidas
          de segurança adequadas para impedir o acesso não autorizado, divulgação, alteração ou
          destruição não autorizada dos dados.
        </p>
        <p class="text-justify indent-8">
          O processamento de dados é realizado utilizando computadores e /ou ferramentas de TI
          habilitadas, seguindo procedimentos organizacionais e meios estritamente relacionados com
          os fins indicados. Além do controlador de dados, em alguns casos, os dados podem ser
          acessados por certos tipos de pessoas envolvidas com a operação do aplicativo
          (administração, vendas, marketing, administração legal do sistema).
        </p>
      </div>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Período de Conservação dos Dados</h3>
        <p class="text-justify indent-8">
          Os dados serão mantidos pelo período necessário para prestar o serviço solicitado pelo
          usuário, ou para os fins descritos neste documento. Mesmo após o término do uso dos nossos
          serviços, esses dados poderão ser mantidos em nossos arquivos apenas para uso estatístico.
        </p>
      </div>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Uso dos Dados Coletados</h3>
        <p class="text-justify indent-8">
          Os dados relativos ao usuário são coletados para permitir que o TôKumSede forneça os seus
          serviços, bem como para os seguintes propósitos: identificar o usuário, contatar, efetuar
          entrega de produtos adquiridos, receber pagamentos, restringir o uso por parte de menores
          e/ou a aquisição de produtos destinados a maiores de 18 anos, como também para aperfeiçoar
          nossos produtos e serviços, ofertando-os de acordo com o perfil do público alvo de cada
          seguimento.
        </p>
        <p class="text-justify indent-8">
          Salvo por meio de petição judicial, conforme descrito na seção correlata, em nenhuma
          hipótese o TôKumSede compartilha ou fornece informações de usuários para terceiros. Todas
          informações coletadas e/ou armazenadas são de uso estrito do TôKumSede, exclusivamente
          para os fins descritos neste documento.
        </p>
      </div>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Proteção de Informações Pessoais</h3>
        <p class="text-justify indent-8">
          O TôKumSedde toma precauções, entre elas medidas administrativas, técnicas e físicas, para
          proteger as suas informações pessoais contra perda, roubo, uso indevido, bem como contra
          acesso não autorizado, divulgação, alteração e destruição.
        </p>
      </div>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Ação Judicial</h3>
        <p class="text-justify indent-8">
          Os dados pessoais dos usuários podem ser utilizados para fins jurídicos pelo controlador
          de dados em juízo ou nas etapas conducentes à possível ação jurídica decorrente de uso
          indevido deste serviço (este aplicativo) ou dos serviços relacionados.
        </p>
        <p class="text-justify indent-8">
          O usuário declara estar ciente de que o controlador dos dados poderá ser obrigado a
          revelar os dados pessoais mediante solicitação das autoridades governamentais.
        </p>
      </div>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Logs do Sistema e Manutenção</h3>
        <p class="text-justify indent-8">
          Para fins de operação e manutenção, este aplicativo poderá coletar arquivos que gravam a
          interação com este aplicativo (logs do sistema) ou usar, para este fim, outros dados
          pessoais (tais como o endereço IP).
        </p>
      </div>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Mudanças nesta Política de Privacidade</h3>
        <p class="text-justify indent-8">
          O controlador de dados se reserva o direito de fazer alterações nesta política de
          privacidade a qualquer momento, mediante comunicação aos seus usuários em sua página web
          acessível em <a href="#privacidade">tks.tokumsede.com.br/#privacidade</a>. Se o usuário
          não concordar com qualquer das alterações da política de privacidade, o usuário deve
          cessar o uso deste serviço (este aplicativo) e pode solicitar ao controlador de dados que
          apague os dados pessoais dele. Salvo disposição em contrário, a atual política de
          privacidade se aplica a todos os dados pessoais dos usuários que o controlador de dados
          tiver.
        </p>
      </div>
      <!-- Sections -->
      <div class="space-y-4">
        <h3 class="font-semibold mt-6 mb-2">Dúvidas e Acessos Futuros</h3>
        <p class="text-justify indent-8">
          Se você tiver algum questionamento ou dúvida com relação à Política de Privacidade do
          TôKumSede ou qualquer prática descrita aqui, entre em contato conosco no endereço Rua
          Nestor Pires de Oliveira, n° 234, Bairro João Rosado de Oliveira – Jericó/PB CEP:
          58830-000, ou através do email contato@tokumsede.com.br. O TôKumSede poderá atualizar a
          sua Política de Privacidade periodicamente. Se fizermos alguma alteração na política
          colocaremos um aviso no nosso site, juntamente com a Política de Privacidade atualizada.
        </p>
      </div>
      <!-- Links -->
      <p class="text-justify indent-8">
        Após a efetivação do seu cadastro você poderá consultar essa política de privacidade a
        qualquer momento no endereço
        <!-- Este link NÃO tem o @click="handlePrivacyLinkClick", então ele sempre tentará abrir e rolar -->
        <a href="#privacidade" class="font-medium text-primary hover:underline">
          tks.tokumsede.com.br/#privacidade
        </a>
      </p>
    </div>
  </section>
</template>

<style scoped>
.indent-8 {
  text-indent: 2rem;
}
</style>
