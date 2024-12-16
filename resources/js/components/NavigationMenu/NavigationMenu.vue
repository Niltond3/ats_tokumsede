<script setup>
import { watch, ref } from 'vue'
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
} from '@/components/ui/navigation-menu'
import { Link, } from '@inertiajs/vue3';
import logo from '@/../../public/images/tokumsede-logo.png';
import { onBeforeRouteUpdate, onBeforeRouteLeave, useRouter, useRoute } from 'vue-router';
import { DialogRegisterProduct } from './components/DialogRegisterProduct';
import DialogRegisterClient from './components/DialogRegisterClient.vue';


const router = useRouter();
// Obtém os parâmetros da rota
const route = useRoute()

// Desestruturando para pegar o parâmetro 'id'

// Observar mudanças no parâmetro 'id'
watch(() => route, (newId, oldId) => {
    console.log(`Parâmetro mudou: de ${oldId} para ${newId}`)
    // Você pode executar qualquer lógica aqui quando o parâmetro mudar
})
// Observar mudanças no parâmetro 'id'
watch(() => route.query, (newId, oldId) => {
    const query = {
        produto: () => console.log('catch')
    }

    if (newId.registro) query[newId.registro]()
})

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
]

const components = [
    //    {
    //        title: 'Cliente',
    //        icon: 'ri-user-add-fill',
    //        href: '/home/register/client',
    //        description:
    //'Formulário de cadastro de um novo Cliente.',
    //    },
    //    {
    //        title: 'Pedido',
    //        icon: 'ri-e-bike-2-fill',
    //        href: '/home/register/order',
    //        description:
    //'Formulário de cadastro de um novo Pedido',
    //    },
    //    {
    //        title: 'Distribuidor',
    //        icon: 'ri-store-3-fill',
    //        href: '/home/register/distribuidor',
    //        description:
    //'Formulário de cadastro de um novo Distribuidor.',
    //    },
    //    {
    //        title: 'Entregador',
    //        icon: 'ri-riding-fill',
    //        href: '/home/register/entregador',
    //        description: 'Formulário de cadastro de um novo Entregador.',
    //    },
    {
        title: 'Produto',
        icon: 'ri-shopping-bag-3-fill',//ri-box-3-fill
        href: '?registro=produto',
        description:
            'Formulário de cadastro de um novo Produto.',
    }
]

function irParaProdutosComFiltro() {
    // Navega para /produtos com parâmetros de consulta
    router.push({
        name: 'home',
        query: { registro: 'produto' }
    });
}



</script>

<template>
    <NavigationMenu>
        <NavigationMenuList>
            <NavigationMenuItem>
                <NavigationMenuTrigger
                    class="text-info/80 hover:text-info transition-colors font-semibold focus:text-accepted">
                    Início
                </NavigationMenuTrigger>
                <NavigationMenuContent>
                    <ul
                        class="grid gap-3 p-6 md:w-[400px] lg:w-[500px] lg:grid-cols-[minmax(0,.75fr)_minmax(0,1fr)] items-center justify-center max-h-[64vh] overflow-auto">
                        <li class="row-span-3">
                            <NavigationMenuLink as-child>
                                <Link
                                    class="flex h-full w-full select-none flex-col justify-end rounded-md bg-gradient-to-b from-muted/50 to-muted p-6 no-underline outline-none focus:shadow-md"
                                    to="/">
                                <img :src="logo" alt="Logo" class="h-full w-auto">
                                <div class="mb-2 mt-4 text-lg font-medium text-info">
                                    Tôkumsede delivery
                                </div>
                                <p class="text-sm leading-tight text-muted-foreground">
                                    Um sistema de gestão e delivery de águas Alkalinas.
                                </p>
                                </Link>
                            </NavigationMenuLink>
                        </li>
                        <li v-for="link in homeLinks" :key="link.title" class="pl-3">
                            <NavigationMenuLink as-child>
                                <Link :href="`/home${link.href}`"
                                    class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground">
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
                    class="text-info/80 hover:text-info transition-colors font-semibold focus:text-accepted">Cadastrar
                </NavigationMenuTrigger>
                <NavigationMenuContent>
                    <ul
                        class="grid w-[90vw] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px] max-h-[64vh] overflow-auto">
                        <li key="product_register">
                            <DialogRegisterProduct />
                        </li>
                        <li key="client_register">
                            <DialogRegisterClient />
                        </li>
                    </ul>
                </NavigationMenuContent>
            </NavigationMenuItem>
        </NavigationMenuList>
    </NavigationMenu>
</template>
