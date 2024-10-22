<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- User profile -->
        {{--@include('templates.application.components.sidebar-profile')--}}
        <!-- End User profile text-->
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li>
                    <router-link to="/" aria-expanded="false" class="navtoggler">
                        <i class="fas fa-home text-center"></i>
                        <span class="hide-menu">{{ Auth::user()->nome }}</span>
                    </router-link>
                    <ul aria-expanded="false" class="collapse">
                    </ul>
                </li>
                @if(auth()->user()->tipoAdministrador != "Entregador")
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-clipboard-list text-center"></i>
                        <span class="hide-menu">Pedidos </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/listaclientes" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link to="/listapedidos" class="navtoggler">Listar</router-link></li>
                        <li><router-link to="/buscarpedido" class="navtoggler">Editar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-users text-center"></i>
                        <span class="hide-menu">Clientes </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/cadastrocliente" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link to="/listarclientes" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->tipoAdministrador == "Administrador")
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-boxes text-center"></i>
                        <span class="hide-menu">Produtos </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/cadastroproduto" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link to="/listaprodutos" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fa fa-address-card text-center"></i>
                        <span class="hide-menu">Distribuidores </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/cadastrodistribuidor" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link id="listadistribuidores" to="/listadistribuidores" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-id-card-alt text-center"></i>
                        <span class="hide-menu">Entregadores </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <!-- <li><router-link to="/cadastroentregador" class="navtoggler">Cadastrar</router-link></li> -->
                        <li><router-link to="/listaentregadores" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-cubes text-center"></i>
                        <span class="hide-menu">Categorias </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/cadastrocategoria" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link to="/listacategorias" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-shipping-fast text-center"></i>
                        <span class="hide-menu">Vendas </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/cadastrocompra" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link to="/listacompras" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-chart-bar text-center"></i>
                        <span class="hide-menu">Relatórios </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/vendas" class="navtoggler">Vendas por Distribuidor</router-link></li>
                        <li><router-link to="/vendasProduto" class="navtoggler">Vendas por Produtos</router-link></li>
                        <li><router-link to="/vendasEntregador" class="navtoggler">Vendas por Entregador</router-link></li>
                        <li><router-link to="/pedidos" class="navtoggler">Pedidos</router-link></li>
                        <li><router-link to="/pedidoEspecifico" class="navtoggler">Pedido Específico</router-link></li>
                        <li><router-link to="/estoque" class="navtoggler">Estoque</router-link></li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->tipoAdministrador == "Distribuidor")
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-boxes text-center"></i>
                        <span class="hide-menu">Produtos </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <!-- <li><router-link to="/cadastrarproduto" class="navtoggler">Cadastrar</router-link></li> -->
                        <li><router-link to="/listaprodutosdist" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fa fa-address-card text-center"></i>
                        <span class="hide-menu">Distribuição </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/atualizacaodistribuidor" class="navtoggler">Editar Informações</router-link></li>
                        <li><router-link to="/feriados" class="navtoggler">Feriados</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-id-card-alt text-center"></i>
                        <span class="hide-menu">Entregadores </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/cadastroentregador" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link to="/listaentregadores" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-shipping-fast text-center"></i>
                        <span class="hide-menu">Compras </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/listacompras" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-chart-bar text-center"></i>
                        <span class="hide-menu">Relatórios </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/vendas" class="navtoggler">Vendas</router-link></li>
                        <li><router-link to="/vendasProduto" class="navtoggler">Vendas por Produto</router-link></li>
                        <li><router-link to="/vendasEntregador" class="navtoggler">Vendas por Entregador</router-link></li>
                        <li><router-link to="/pedidos" class="navtoggler">Pedidos</router-link></li>
                        <li><router-link to="/pedidoEspecifico" class="navtoggler">Pedido Específico</router-link></li>
                        <li><router-link to="/estoque" class="navtoggler">Estoque</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-user text-center"></i>
                        <span class="hide-menu">Usuário </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <!-- <li><router-link to="/cadastrarproduto" class="navtoggler">Cadastrar</router-link></li> -->
                        <li><router-link to="/mudarSenha" class="navtoggler">Mudar Senha</router-link></li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->tipoAdministrador == "Entregador")
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-clipboard-list text-center"></i>
                        <span class="hide-menu">Pedidos </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/listapedidos" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->tipoAdministrador == "Atendente")
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fa fa-address-card text-center"></i>
                        <span class="hide-menu">Distribuidores </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link id="listadistribuidores" to="/listadistribuidores" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-id-card-alt text-center"></i>
                        <span class="hide-menu">Entregadores </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/listaentregadores" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li>
                @endif
                <!-- <li>
                    <a class="has-arrow" href="#" aria-expanded="false">
                        <i class="fas fa-handshake text-center"></i>
                        <span class="hide-menu">Parceiros </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li><router-link to="/example-component" class="navtoggler">Cadastrar</router-link></li>
                        <li><router-link to="/home" class="navtoggler">Listar</router-link></li>
                    </ul>
                </li> -->

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
    <!-- Bottom points-->
    <!-- <div class="sidebar-footer">
        <a href="" class="link" data-toggle="tooltip" title="Settings"><i class="ti-settings"></i></a>
        <a href="" class="link" data-toggle="tooltip" title="Email"><i class="mdi mdi-gmail"></i></a>
        <a href="/logout" class="link" data-toggle="tooltip" title="Logout"><i class="mdi mdi-power"></i></a>
    </div> -->
    <!-- End Bottom points-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
