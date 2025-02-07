<script setup>
// Vue core
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Components
import Dashboard from '../Dashboard.vue';
import { TableClientes } from './DataTableClientes';
import { TablePedidos } from './DataTablePedidos';

// UI Components
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

// Composables & Utils
import { useToggleTabs } from './useTabs';
import { useQzTray } from '@/composables/useQzTray';
import useIsMobile from '@/composables/useIsMobile';
import renderToast from '@/components/renderPromiseToast';

const { connect } = useQzTray();
const { detectDevice, isMobile } = useIsMobile();
const page = usePage();

const typeAdmin = page.props.auth.user.tipoAdministrador;

const getTypeAdmin = {
  Distribuidor: {
    tab: 'estatisticas',
    tabName: 'Estatísticas',
    description: 'Vistualize as estatísticas das vendas',
  },
  Administrador: {
    tab: 'clientes',
    tabName: 'Clientes',
    description:
      'Cadastre um novo cliente, edite um já existente ou realizar um pedido em nome de um cliente cadastrado',
  },
};

const { tab, tabName, description } = getTypeAdmin[typeAdmin];

const { activeTab, setActiveTab } = useToggleTabs(tab);

const handleSetActiveTab = (tab) => setActiveTab(tab);

// Função para conectar ao QZ Tray
const connectQZTray = () => {
  const promise = connect();
  renderToast(
    promise,
    'Conectando ao QZ Tray',
    'Conectado ao QZ Tray',
    'QZ não encontrado! Inicie-o, atualize a página e tente novamente',
  );
};

onMounted(() => {
  detectDevice();
  !isMobile.value && connectQZTray();
});
</script>

<template>
  <div class="row">
    <Tabs default-value="account" :default-value="tab" :model-value="activeTab">
      <TabsList class="grid w-full grid-cols-2">
        <TabsTrigger :value="tab" @Click="handleSetActiveTab(tab)">
          {{ tabName }}
        </TabsTrigger>
        <TabsTrigger
          value="pedidos"
          class="*:overflow-visible"
          @Click="handleSetActiveTab('pedidos')"
        >
          <span class="relative">
            <div
              class="absolute hidden items-center justify-center w-6 h-6 text-xs font-bold text-white bg-danger border-2 border-white rounded-full -top-3 -right-6 -end-2 dark:border-gray-900 animate-pulse m-auto transition-all [transition-behavior:allow-discrete]"
            >
              !
            </div>
            Pedidos
          </span>
        </TabsTrigger>
      </TabsList>
      <TabsContent :value="tab">
        <Card>
          <CardHeader>
            <CardTitle>
              <span class="sr-only">Clientes</span>
            </CardTitle>
            <CardDescription>
              {{ description }}
            </CardDescription>
          </CardHeader>
          <CardContent class="">
            <TableClientes v-if="typeAdmin === 'Administrador'" :set-tab="setActiveTab" />
            <Dashboard v-else />
          </CardContent>
        </Card>
      </TabsContent>
      <TabsContent value="pedidos">
        <Card>
          <CardHeader>
            <CardTitle>
              <span class="sr-only">Pedidos</span>
            </CardTitle>
            <CardDescription> Visualize e gerencie todos os pedios feitos hoje </CardDescription>
          </CardHeader>
          <CardContent
            class="[&_.dtsp-titleRow]:h-0 [&_.dtsp-nameColumn]:border-none [&_.dtsp-nameColumn>div]:flex [&_.dtsp-nameColumn>div]:items-center"
          >
            <TablePedidos :set-tab="setActiveTab" />
          </CardContent>
        </Card>
      </TabsContent>
    </Tabs>
  </div>
</template>
