
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import "./bootstrap";

window.Vue = require('vue');

//Importar Dependências
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)
//Declaração do array utilizando o let (escopo de bloco)
let routes = [
    { path: '/', component: require('./components/HomeComponent.vue') },
    { path: '/listadistribuidores', component: require('./components/views/distribuidor/listaDistribuidores.vue') },
    { path: '/cadastrodistribuidor', component: require('./components/views/distribuidor/cadastroDistribuidor.vue') },
    { path: '/feriados', component: require('./components/views/distribuidor/feriados.vue') },
    { path: '/atualizacaodistribuidor', component: require('./components/views/distribuidor/atualizacaoDistribuidor.vue') },
    { path: '/buscarpedido', component: require('./components/views/pedido/buscarPedido.vue') },
    { path: '/editarpedido', component: require('./components/views/pedido/editarPedido.vue') },
    { path: '/listapedidos', component: require('./components/views/pedido/listaPedidos.vue') },
    { path: '/listaclientes', component: require('./components/views/pedido/listaClientes.vue') },
    { path: '/enderecoscliente', component: require('./components/views/pedido/enderecosCliente.vue') },
    { path: '/cadastropedido', component: require('./components/views/pedido/cadastroPedido.vue') },
    { path: '/cadastrocliente', component: require('./components/views/cliente/cadastro.vue') },
    { path: '/listarclientes', component: require('./components/views/cliente/lista.vue') },
    { path: '/cadastroentregador', component: require('./components/views/entregador/cadastroEntregador.vue') },
    { path: '/listaentregadores', component: require('./components/views/entregador/listaEntregadores.vue') },
    { path: '/cadastrocategoria', component: require('./components/views/categoria/cadastroCategoria.vue') },
    { path: '/listacategorias', component: require('./components/views/categoria/listaCategorias.vue') },
    { path: '/listacompras', component: require('./components/views/compras/listaCompras.vue') },
    { path: '/cadastrocompra', component: require('./components/views/compras/cadastroCompra.vue') },
    { path: '/cadastroproduto', component: require('./components/views/produto/cadastroProduto.vue') },
    { path: '/listaprodutos', component: require('./components/views/produto/listaProdutos.vue') },
    { path: '/listaprodutosdist', component: require('./components/views/produto/listaProdutosDist.vue') },
    { path: '/precos/:idProduto/:idEstoque', component: require('./components/views/produto/precos.vue') },
    { path: '/mudarSenha', component: require('./components/views/usuario/mudarSenha.vue') },
    { path: '/vendas', component: require('./components/views/relatorios/vendas.vue') },
    { path: '/vendasProduto', component: require('./components/views/relatorios/vendasProduto.vue') },
    { path: '/vendasEntregador', component: require('./components/views/relatorios/vendasEntregador.vue') },
    { path: '/pedidos', component: require('./components/views/relatorios/pedidos.vue') },
    { path: '/pedidoEspecifico', component: require('./components/views/relatorios/pedidoEspecifico.vue') },
    { path: '/estoque', component: require('./components/views/relatorios/estoque.vue') },
    { path: '/example-component', component: require('./components/ExampleComponent.vue') },
]
//instância router, onde são passadas as opções
const router = new VueRouter({
    //mode: 'history',//remove '#' URL
    routes //`routes: routes`
})
/**
 * Em seguida, vamos criar uma nova instância do aplicativo Vue e anexá-la a
 * a página. Então, você pode começar a adicionar componentes a este aplicativo
 * ou personalize o andaime JavaScript para atender às suas necessidades exclusivas.
 */
Vue.component('/', require('./components/HomeComponent.vue'));
Vue.component('listadistribuidores', require('./components/views/distribuidor/listaDistribuidores.vue'));
Vue.component('cadastrodistribuidor', require('./components/views/distribuidor/cadastroDistribuidor.vue'));
Vue.component('feriados', require('./components/views/distribuidor/feriados.vue'));
Vue.component('atualizacaodistribuidor', require('./components/views/distribuidor/atualizacaoDistribuidor.vue'));
Vue.component('buscarPedido', require('./components/views/pedido/buscarPedido.vue'));
Vue.component('editarPedido', require('./components/views/pedido/editarPedido.vue'));
Vue.component('listapedidos', require('./components/views/pedido/listaPedidos.vue'));
Vue.component('listaclientes', require('./components/views/pedido/listaClientes.vue'));
Vue.component('enderecoscliente', require('./components/views/pedido/enderecosCliente.vue'));
Vue.component('cadastropedido', require('./components/views/pedido/cadastroPedido.vue'));
Vue.component('cadastrocliente', require('./components/views/cliente/cadastro.vue'));
Vue.component('listarclientes', require('./components/views/cliente/lista.vue'));
Vue.component('cadastroentregador', require('./components/views/entregador/cadastroEntregador.vue'));
Vue.component('listaentregadores', require('./components/views/entregador/listaEntregadores.vue'));
Vue.component('cadastrocategoria', require('./components/views/categoria/cadastroCategoria.vue'));
Vue.component('listacategorias', require('./components/views/categoria/listaCategorias.vue'));
Vue.component('listacompras', require('./components/views/compras/listaCompras.vue'));
Vue.component('cadastrocompra', require('./components/views/compras/cadastroCompra.vue'));
Vue.component('cadastroproduto', require('./components/views/produto/cadastroProduto.vue'));
Vue.component('listaprodutos', require('./components/views/produto/listaProdutos.vue'));
Vue.component('listaprodutosdist', require('./components/views/produto/listaProdutosDist.vue'));
Vue.component('precos', require('./components/views/produto/precos.vue'));
Vue.component('mudarSenha', require('./components/views/usuario/mudarSenha.vue'));
Vue.component('vendas', require('./components/views/relatorios/vendas.vue'));
Vue.component('vendasProduto', require('./components/views/relatorios/vendasProduto.vue'));
Vue.component('vendasEntregador', require('./components/views/relatorios/vendasEntregador.vue'));
Vue.component('pedidos', require('./components/views/relatorios/pedidos.vue'));
Vue.component('pedidoEspecifico', require('./components/views/relatorios/pedidoEspecifico.vue'));
Vue.component('estoque', require('./components/views/relatorios/estoque.vue'));
Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('alert-component', require('./components/AlertComponent'));
Vue.component('carousel-component', require('./components/CarouselComponent'));

//Inicializando na const app:
const app = new Vue({
    el: '#main-wrapper',
    router,
    data: {
        nomeCliente: '',
        precoAcertado: '',
        idCliente: '',
        idEndereco: '',
        idPedido: '',
        refresh: true,
        tablePedidosPendentes: '',
        tablePedidosAceitos: '',
        tablePedidosEntregues: '',
        tablePedidosCancelados: '',
        tablePedidosAgendados: '',
        tableEnderecos: '',
        tablePedidos: '',
        tableFeriados: '',
        audio: '',
        ultimoPedido: '',
        search: ''
    }
});
