<template>
    <!-- Componente Dialog para gerenciar entregadores -->
    <Dialog :open="isOpen" @update:open="handleDialogOpen">
      <!-- Botão para abrir o diálogo com ícone e título -->
      <DialogTrigger icon="ri-truck-line" title="Entregadores" />

      <!-- Conteúdo do diálogo -->
      <DialogContent class="flex gap-4 flex-col">
        <!-- Cabeçalho do diálogo com título e descrição -->
        <DialogHeader>
          <DialogTitle class="flex gap-3 text-lg text-info items-center">
            <i class="ri-truck-line"></i>
            <span>Gerenciar Entregadores</span>
          </DialogTitle>
          <DialogDescription class="py-2">
            Cadastrar, atualizar, excluir e atribuir entregadores.
          </DialogDescription>
        </DialogHeader>

        <!-- Seção de filtros -->
        <div class="flex flex-col gap-4">
          <!-- Campo de busca para filtrar por nome ou placa -->
          <div class="relative">
            <i class="ri-search-2-fill absolute left-2 top-1/2 -translate-y-1/2 text-info"></i>
            <Input
              v-model="filterText"
              type="text"
              class="pl-8"
              placeholder="Pesquise por nome ou placa do veículo"
            />
          </div>
          <!-- Abas para filtrar por distribuidora.
               Essa seção só é exibida se o usuário não for Distribuidor -->
          <div
            v-if="tipoAdministrador !== 'Distribuidor'"
            class="flex gap-2 max-w-full overflow-x-scroll scrollbar scrollbar-corner-rounded !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2 border-slate-00 mb-3 pb-2"
          >
            <!-- Botão para remover filtro (exibe todos) -->
            <Button
              class="px-2 py-0.5 text-info"
              :class="{ 'bg-primary text-white': filterDistributorId === null }"
              :variant="filterDistributorId === null ? 'default' : 'primary'"
              @click="filterDistributorId = null"
            >
              Todos
            </Button>
            <!-- Botões para cada distribuidora disponível -->
            <Button
              v-for="distribuidor in sortedDistribuidores"
              :key="distribuidor.id"
              class="px-2 py-0.5 text-info"
              :class="{ 'bg-primary text-white': filterDistributorId === distribuidor.id }"
              :variant="filterDistributorId === distribuidor.id ? 'default' : 'primary'"
              @click="filterDistributorId = distribuidor.id"
            >
              {{ formatDistributorName(distribuidor.nome) }}
            </Button>
          </div>
        </div>

        <!-- Lista de entregadores -->
        <div class="border-y py-2 overflow-hidden border-slate-200 min-h-56">
          <ul
            class="overflow-y-auto max-h-64 scrollbar scrollbar-corner-rounded !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2"
          >
            <!-- Itera sobre os entregadores filtrados -->
            <li
              v-for="deliveryman in filteredDeliverymen"
              :key="deliveryman.id"
              class="group p-2 rounded hover:bg-info/80 transition-all text-info text-base flex gap-2 items-center group/line duration-300"
            >
              <!-- Botão para editar o entregador -->
              <div class="mr-auto border-r border-slate-300 pr-3 flex gap-2">
                <button
                  class="relative !size-10 text-2xl shadow-md rounded-full transition-all group-hover/line:bg-white hover:shadow-lg text-info/60 hover:text-info/100"
                  @click="editDeliveryman(deliveryman)"
                >
                  <i
                    class="ri-pencil-fill text-sm pointer-events-none select-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                  ></i>
                </button>
              </div>
              <!-- Informações do entregador -->
              <div class="flex flex-col group-hover:text-white w-full">
                <span class="text-base">{{ deliveryman.nome }}</span>
                <span class="text-sm text-info/80 group-hover:text-white">{{ deliveryman.telefone }}</span>
                <span class="text-sm text-info/80 group-hover:text-white">
                  Placa: {{ deliveryman.placaVeiculo }}
                </span>
                <span class="text-sm text-info/80 group-hover:text-white">
                  Distribuidor:
                  {{ deliveryman.distribuidor ? formatDistributorName(deliveryman.distribuidor.nome) : 'Não atribuído' }}
                </span>
              </div>
              <!-- Botão para excluir o entregador -->
              <div class="ml-auto border-l border-slate-300 pl-3 flex gap-2">
                <button
                  variant="destructive"
                  class="relative !size-10 text-2xl shadow-md rounded-full transition-all group-hover/line:bg-white hover:shadow-lg text-info/60 hover:text-info/100"
                  @click="deleteDeliveryman(deliveryman.id)"
                >
                  <i
                    class="ri-delete-bin-6-fill text-sm pointer-events-none select-none absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
                  ></i>
                </button>
              </div>
            </li>
          </ul>
        </div>

        <!-- Formulário para criação/edição de entregador -->
        <Form
          v-slot="{ validate, values, setFieldValue }"
          :key="deliverymanDetails.id ? `edit-deliveryman-${deliverymanDetails.id}` : 'new-deliveryman'"
          as=""
          :validation-schema="toTypedSchema(formSchema)"
          :initial-values="deliverymanDetails || {}"
        >
          <form @submit="(event) => handleSubmit(event, values, validate)">
            <div class="flex flex-col gap-5">
              <!-- Campo: Nome do entregador -->
              <FormField v-slot="{ componentField }" name="nome">
                <FormItem>
                  <FormControl>
                    <Input v-bind="componentField" type="text" placeholder="Nome do entregador" />
                  </FormControl>
                  <FormLabel class="-top-6 peer-placeholder-shown:-top-4">Nome</FormLabel>
                  <FormMessage />
                </FormItem>
              </FormField>

              <!-- Campo: Telefone -->
              <FormField v-slot="{ componentField }" name="telefone">
                <FormItem>
                  <FormControl>
                    <Input v-bind="componentField" type="text" placeholder="Telefone" />
                  </FormControl>
                  <FormLabel>Telefone</FormLabel>
                  <FormMessage />
                </FormItem>
              </FormField>

              <!-- Campo: Placa do veículo -->
              <FormField v-slot="{ componentField }" name="placaVeiculo">
                <FormItem>
                  <FormControl>
                    <Input v-bind="componentField" type="text" placeholder="Placa do veículo" />
                  </FormControl>
                  <FormLabel>Placa do Veículo</FormLabel>
                  <FormMessage />
                </FormItem>
              </FormField>

              <!-- Campo: Seleção de Distribuidora
                   Exibido apenas se o usuário não for Distribuidor ou se estiver criando um novo entregador -->
              <FormField
                v-if="tipoAdministrador !== 'Distribuidor' || !deliverymanDetails.id"
                v-slot="{ field }"
                name="idDistribuidor"
              >
                <FormItem>
                  <FormControl>
                    <select
                      :value="field.value"
                      class="border rounded-md border-slate-300 py-1 focus:border-info/60 focus:ring-info/60 focus-visible:!ring-info/60 focus-visible:border-info/60 pr-8 pl-2"
                      :disabled="tipoAdministrador === 'Distribuidor' && deliverymanDetails.id"
                      @change="(e) => setFieldValue('idDistribuidor', e.target.value ? Number(e.target.value) : undefined)"
                    >
                      <option value="" class="text-info/60">Selecione um distribuidor</option>
                      <!-- Itera sobre todas as distribuidoras -->
                      <option
                        v-for="distribuidor in distribuidoresList"
                        :key="distribuidor.id"
                        :value="distribuidor.id"
                        :disabled="tipoAdministrador === 'Distribuidor' && distribuidor.id !== userDistributorId"
                      >
                        {{ formatDistributorName(distribuidor.nome) }}
                      </option>
                    </select>
                  </FormControl>
                  <FormLabel>Distribuidor</FormLabel>
                  <FormMessage />
                </FormItem>
              </FormField>

              <!-- Campo oculto para garantir que, se o usuário for Distribuidor e estiver editando,
                   o idDistribuidor seja sempre o do usuário -->
              <input
                v-if="tipoAdministrador === 'Distribuidor' && deliverymanDetails.id"
                type="hidden"
                :value="userDistributorId"
                name="idDistribuidor"
              />

              <!-- Botão de envio do formulário -->
              <div>
                <Button type="submit" size="sm" :disabled="disabledButton">
                  {{ deliverymanDetails.id ? 'Atualizar' : 'Cadastrar' }}
                </Button>
              </div>
            </div>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  </template>

  <script setup>
  /*
    Importações necessárias:
    - Funções do Vue para reatividade, ciclo de vida e computados.
    - Biblioteca para validação com Zod.
    - usePage do Inertia para acessar as propriedades do usuário autenticado.
    - Componentes e composables personalizados.
  */
  import { ref, computed, onMounted, watch, nextTick } from 'vue';
  import { toTypedSchema } from '@vee-validate/zod';
  import { usePage } from '@inertiajs/vue3';

  // Componentes de UI personalizados
  import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
  } from '@/components/ui/dialog';
  import DialogTrigger from '@/components/dialogs/DialogTrigger.vue';
  import {
    Form,
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormMessage,
  } from '@/components/ui/form';
  import { Input } from '@/components/ui/input';
  import { Button } from '@/components/ui/button';

  // Composables para a lógica de gerenciamento de entregadores e filtro por distribuidora
  import { useDeliverymanManagement } from '@/composables/useDeliverymanManagement';
  import { useDistributorFilter } from '@/composables/useDistributorFilter';

  // Obtemos os dados do usuário autenticado via Inertia
  const page = usePage();
  const { tipoAdministrador, idDistribuidor: userDistributorId, nome: administratorName } = page.props.auth.user;

  /*
    Desestruturação das variáveis reativas e funções do composable de gerenciamento de entregadores.
    Obs.: A lógica de CRUD, validação e manipulação do diálogo é gerenciada no composable.
  */
  const {
    isOpen,
    deliverymanDetails,
    deliverymenList,
    distribuidoresList,
    disabledButton,
    formSchema,
    handleDialogOpen,
    handleSubmit: originalHandleSubmit,
    fetchDeliverymen,
    deleteDeliveryman,
    editDeliveryman: originalEditDeliveryman,
  } = useDeliverymanManagement();

  // Variável reativa para armazenar o texto do filtro (busca por nome ou placa)
  const filterText = ref('');

  // Utilizamos o composable de filtro para gerenciar a seleção e ordenação das distribuidoras
  const { filterDistributorId, sortedDistribuidores, formatDistributorName } = useDistributorFilter(distribuidoresList);

  // Se o usuário for Distribuidor, definimos o filtro padrão para exibir apenas os entregadores da sua distribuidora
  watch(
    () => userDistributorId,
    (newId) => {
      if (tipoAdministrador === 'Distribuidor' && newId) {
        filterDistributorId.value = newId;
      }
    },
    { immediate: true }
  );

  /*
    Propriedade computada que filtra a lista de entregadores.
    Caso o usuário seja Distribuidor, apenas os entregadores cadastrados na mesma distribuidora serão exibidos.
    Também é aplicada a lógica de busca por nome e placa.
  */
  const filteredDeliverymen = computed(() => {
    // Se for Distribuidor, filtramos a lista com base no idDistribuidor
    const deliverymenToFilter =
      tipoAdministrador === 'Distribuidor'
        ? deliverymenList.value.filter(
            (d) =>
              (d.distribuidor && d.distribuidor.id === userDistributorId) ||
              d.idDistribuidor === userDistributorId
          )
        : deliverymenList.value;

    return deliverymenToFilter.filter((deliveryman) => {
      const text = filterText.value.toLowerCase();
      // Verifica se o nome ou a placa contém o texto do filtro
      const matchesText =
        !text ||
        (deliveryman.nome && deliveryman.nome.toLowerCase().includes(text)) ||
        (deliveryman.placaVeiculo && deliveryman.placaVeiculo.toLowerCase().includes(text));
      // Se houver filtro por distribuidora, garante que o entregador pertença à distribuidora selecionada
      const matchesDistributor =
        tipoAdministrador === 'Distribuidor' ||
        filterDistributorId.value === null ||
        (deliveryman.distribuidor && deliveryman.distribuidor.id === filterDistributorId.value) ||
        deliveryman.idDistribuidor === filterDistributorId.value;
      return matchesText && matchesDistributor;
    });
  });

  /*
    Função wrapper para edição de entregador.
    Se o usuário for Distribuidor, força o idDistribuidor do entregador a ser o do usuário,
    garantindo que a distribuidora não seja alterada durante a edição.
  */
  const editDeliverymanWrapper = (deliveryman) => {
    // Chama a função original de edição
    originalEditDeliveryman(deliveryman);
    // Se for Distribuidor, utiliza nextTick para assegurar que os dados já foram atualizados
    if (tipoAdministrador === 'Distribuidor') {
      nextTick(() => {
        if (deliverymanDetails.value) {
          deliverymanDetails.value.idDistribuidor = userDistributorId;
        }
      });
    }
  };
  const editDeliveryman = editDeliverymanWrapper;

  /*
    Função wrapper para o envio do formulário.
    Se o usuário for Distribuidor, força o envio com o idDistribuidor do usuário.
  */
  const handleSubmitWrapper = (event, values, validate) => {
    if (tipoAdministrador === 'Distribuidor') {
      values.idDistribuidor = userDistributorId;
    }
    return originalHandleSubmit(event, values, validate);
  };
  const handleSubmit = handleSubmitWrapper;

  // Ao montar o componente, buscamos a lista de entregadores via API
  onMounted(() => {
    fetchDeliverymen();
  });
  </script>
