<!-- ============================================================== -->
<!-- Topbar header - style you can find in pages.scss -->
<!-- ============================================================== -->
<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-header">
            <a class="navbar-brand" href="/">
                <!-- Logo icon -->
                <b>
                    <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                    <!-- Dark Logo icon -->
                    <img src="/vendor/wrappixel/material-pro/4.1.0/assets/images/logo-icon.png"
                         alt="homepage"
                         class="dark-logo"/>
                    <!-- Light Logo icon -->
                    <img src="/vendor/wrappixel/material-pro/4.1.0/assets/images/logo.png"
                         alt="homepage" class="light-logo" />
                </b>
                <!--End Logo icon -->
                <!-- Logo text -->
                <span style="float: right;">
                    {{-- <!-- dark Logo text -->
                    <img src="/vendor/wrappixel/material-pro/4.1.0/assets/images/logo-text.png"
                         alt="homepage"
                         class="dark-logo"/> --}}
                    <!-- Light Logo text -->
                    <h3 style="color:#ffffff; margin:25px 0 0 10px;">Delivery</h3>
                </span>
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- This is  -->
                @if(true)
                <li class="nav-item">
                    <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                       href="javascript:void(0)"
                    >
                        <i class="mdi mdi-menu"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark"
                       href="javascript:void(0)"
                    >
                        <i class="ti-menu"></i>
                    </a>
                </li>
                @endif
                {{-- <!-- ============================================================== -->
                <!-- Search -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-search')
                <!-- ============================================================== -->
                <!-- End Search -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Megamenu -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-megamenu')
                <!-- ============================================================== -->
                <!-- End Megamenu -->
                <!-- ============================================================== --> --}}
            </ul>
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- This is  -->
                @if(true)
                <li class="nav-item">
                    <div id="alertaPedidoTop" class="float-left" style="padding-right: 15px;">
                        <span class="heartbit" style="margin-top: 60px;"></span>
                        <span class="point" style="margin-top: 60px;"></span>
                    </div>
                    <router-link class="nav-link active d-none" data-toggle="tab" to="/listapedidos" role="tab" id="alertaTopbar" title="Listar Pedidos">
                        <i class="fas fa-bell"></i>
                    </router-link>
                    <a class="nav-link active" data-toggle="tab" role="tab" id="audio">
                        <i class="mdi mdi-volume-off"></i>
                    </a>
                </li>
                @endif
            </ul>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <ul class="navbar-nav my-lg-0">
                {{-- <!-- ============================================================== -->
                <!-- Comment -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-comments')
                <!-- ============================================================== -->
                <!-- End Comment -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Messages -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-messages')
                <!-- ============================================================== -->--}}
                <!-- End Messages -->
                <!-- ============================================================== -->
                <form id="form-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}<input type="hidden" name="mobile" id="input-form-logout"/>
                    
                </form><!--diferencia 'Sair' mobile true ou mobile false para zerar token fcm-->
                <li class="nav-item">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('form-logout').submit();" class="nav-link text-muted waves-effect waves-dark"><i class="mdi mdi-logout" aria-hidden="true"></i>Sair</a>
                </li><!-- ============================================================== -->
                <!-- Profile -->
                <!-- ============================================================== -->
               {{-- @includeWhen(true, 'templates.application.components.navbar-profile')
                <!-- ============================================================== -->
                <!-- End Profile -->
                <!-- ============================================================== -->
                 <!-- ============================================================== -->
                <!-- Language -->
                <!-- ============================================================== -->
                @includeWhen(true, 'templates.application.components.navbar-lang')
                <!-- ============================================================== -->
                <!-- End Language -->
                <!-- ============================================================== --> --}}
            </ul>
        </div>
    </nav>
</header>
<!-- ============================================================== -->
<!-- End Topbar header -->
<!-- ============================================================== -->
