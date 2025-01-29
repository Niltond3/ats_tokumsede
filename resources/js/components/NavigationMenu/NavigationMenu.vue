<script setup>
import { watch } from 'vue';
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
} from '@/components/ui/navigation-menu';
import { usePage } from '@inertiajs/vue3';

import { Link } from '@inertiajs/vue3';
import logo from '@/../../public/images/tokumsede-logo.png';
import { useRoute } from 'vue-router';
import { DialogRegisterProduct } from './components/DialogRegisterProduct';
import DialogRegisterClient from './components/DialogRegisterClient.vue';
import { DialogRegisterPrices } from '@/components/DialogRegisterPrices';
import DialogTrigger from './components/DialogTrigger.vue';
import DialogReportStock from './components/reports/DialogReportStock.vue';
import DialogReportOrders from './components/reports/DialogReportOrders.vue';

import { dialogState } from '@/hooks/useToggleDialog.js';
import DialogStockMerge from './components/stock/DialogStockMerge.vue';

const { isOpen: openRegisterPrices, toggleDialog: toggleRegisterPrices } = dialogState();
const { isOpen: openRegisterReportOrders, toggleDialog: toggleReportOrders } = dialogState();
const { isOpen: openRegisterReportStock, toggleDialog: toggleReportStock } = dialogState();
const { isOpen: openStockMerge, toggleDialog: toggleStockMerge } = dialogState();

const page = usePage();
const { user } = page.props.auth;
const { tipoAdministrador } = user;
// Obtém os parâmetros da rota
const route = useRoute();

// Observar mudanças no parâmetro 'id'
watch(
  () => route,
  (newId, oldId) => {
    console.log(`Parâmetro mudou: de ${oldId} para ${newId}`);
    // Você pode executar qualquer lógica aqui quando o parâmetro mudar
  },
);
// Observar mudanças no parâmetro 'id'
watch(
  () => route.query,
  (newId) => {
    const query = {
      produto: () => console.log('catch'),
    };

    if (newId.registro) query[newId.registro]();
  },
);

const homeLinks = [
  {
    title: 'Gestão',
    icon: 'ri-tools-fill',
    href: '',
    description: 'Gestão de clientes e entregas.',
  },
  {
    title: 'Informações',
    icon: 'ri-dashboard-fill',
    href: '/dashboard',
    description: 'Informações de faturamento, clientes ativos e entregas.',
  },
  // {
  //     title: 'Clientes',
  //     icon: 'ri-user-fill',
  //     href: '/clientes',
  //     description: 'Todos os clientes cadastrados e ativos.',
  // },
  // {
  //     title: 'Pedidos',
  //     icon: 'ri-e-bike-2-fill',
  //     href: '/pedidos',
  //     description: 'Entregas de águas Alkalinas.',
  // },
];
</script>

<template>
  <NavigationMenu>
    <NavigationMenuList>
      <NavigationMenuItem>
        <NavigationMenuTrigger
          class="text-info/80 hover:text-info transition-colors font-semibold focus:text-accepted"
        >
          Início
        </NavigationMenuTrigger>
        <NavigationMenuContent>
          <ul
            class="grid gap-3 p-6 md:w-[400px] lg:w-[500px] lg:grid-cols-[minmax(0,.75fr)_minmax(0,1fr)] items-center justify-center max-h-[64vh] overflow-auto"
          >
            <li class="row-span-3">
              <NavigationMenuLink as-child>
                <Link
                  class="flex h-full w-full select-none flex-col justify-end rounded-md bg-gradient-to-b from-muted/50 to-muted p-6 no-underline outline-none focus:shadow-md"
                  to="/"
                >
                  <img :src="logo" alt="Logo" class="h-full w-auto" />
                  <div class="mb-2 mt-4 text-lg font-medium text-info">Tôkumsede delivery</div>
                  <p class="text-sm leading-tight text-muted-foreground">
                    Um sistema de gestão e delivery de águas Alkalinas.
                  </p>
                </Link>
              </NavigationMenuLink>
            </li>
            <li v-for="link in homeLinks" :key="link.title" class="pl-3">
              <NavigationMenuLink as-child>
                <Link
                  :href="`/home${link.href}`"
                  class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                >
                  <div class="text-sm font-medium leading-none text-info flex items-center">
                    <i :class="link.icon" class="mr-2"></i>
                    {{ link.title }}
                  </div>
                  <p class="line-clamp-2 text-sm leading-snug text-muted-foreground">
                    {{ link.description }}
                  </p>
                </Link>
              </NavigationMenuLink>
            </li>
          </ul>
        </NavigationMenuContent>
      </NavigationMenuItem>
      <NavigationMenuItem>
        <NavigationMenuTrigger
          class="text-info/80 hover:text-info transition-colors font-semibold focus:text-accepted"
          >Cadastrar
        </NavigationMenuTrigger>
        <NavigationMenuContent>
          <ul
            class="grid w-[90vw] gap-3 p-4 md:w-[500px] md:grid-cols-3 lg:w-[600px] max-h-[64vh] overflow-auto"
          >
            <li key="product_register" class="self-center justify-self-center">
              <DialogRegisterProduct />
            </li>
            <li key="client_register" class="self-center justify-self-center">
              <DialogRegisterClient />
            </li>
            <li key="client_price" class="self-center justify-self-center">
              <DialogRegisterPrices
                :isOpen="openRegisterPrices"
                :toggleDialog="toggleRegisterPrices"
                :clientId="null"
              >
                <template #trigger>
                  <DialogTrigger icon="ri-price-tag-3-fill" title="Preço" />
                </template>
              </DialogRegisterPrices>
            </li>
            <li
              v-if="tipoAdministrador === 'Administrador'"
              key="stock_merge"
              class="self-center justify-self-center"
            >
              <DialogStockMerge :isOpen="openStockMerge" :toggleDialog="toggleStockMerge">
                <template #trigger>
                  <DialogTrigger
                    icon="ri-git-merge-fill"
                    title="Unificar Estoques"
                    description="Combine estoques de múltiplos distribuidores"
                  />
                </template>
              </DialogStockMerge>
            </li>
          </ul>
        </NavigationMenuContent>
      </NavigationMenuItem>
      <NavigationMenuItem>
        <NavigationMenuTrigger
          class="text-info/80 hover:text-info transition-colors font-semibold focus:text-accepted"
        >
          Relatórios
        </NavigationMenuTrigger>
        <NavigationMenuContent>
          <ul
            class="grid w-[90vw] gap-5 p-4 md:w-[500px] md:grid-cols-3 lg:w-[600px] max-h-[64vh] overflow-auto"
          >
            <li key="orders_report" class="self-center justify-self-center">
              <DialogReportOrders
                :isOpen="openRegisterReportOrders"
                :toggleDialog="toggleReportOrders"
              >
                <template #trigger>
                  <DialogTrigger
                    icon="ri-file-list-3-line"
                    title="Relatório de Pedidos"
                    description="Visualize todos os pedidos por período e status"
                  />
                </template>
              </DialogReportOrders>
            </li>
            <li key="stock_report" class="self-center justify-self-center">
              <DialogReportStock
                :isOpen="openRegisterReportStock"
                :toggleDialog="toggleReportStock"
              >
                <template #trigger>
                  <DialogTrigger
                    icon="ri-store-3-fill"
                    title="Relatório de Estoque"
                    description="Controle de estoque por distribuidor"
                  />
                </template>
              </DialogReportStock>
            </li>
            <!-- <li key="sales_report">
                            <DialogReportSales>
                                <template #trigger>
                                    <DialogTrigger icon="ri-money-dollar-circle-line" title="Relatório de Vendas"
                                        description="Análise de vendas por distribuidor e período" />
                                </template>
                            </DialogReportSales>
                        </li> -->
            <!-- <li key="product_sales_report">
                            <DialogReportProductSales>
                                <template #trigger>
                                    <DialogTrigger icon="ri-shopping-bag-line" title="Vendas por Produto"
                                        description="Relatório de vendas detalhado por produto" />
                                </template>
                            </DialogReportProductSales>
                        </li> -->
            <!-- <li key="delivery_report">
                            <DialogReportDelivery>
                                <template #trigger>
                                    <DialogTrigger icon="ri-bike-line" title="Relatório de Entregadores"
                                        description="Desempenho e entregas por entregador" />
                                </template>
                            </DialogReportDelivery>
                        </li> -->
          </ul>
        </NavigationMenuContent>
      </NavigationMenuItem>
    </NavigationMenuList>
  </NavigationMenu>
</template>
