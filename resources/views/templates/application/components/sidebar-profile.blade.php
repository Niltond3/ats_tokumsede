<div class="user-profile"
     style="background: url({{IMG}}/background/2.gif) no-repeat;"
> 
    <!-- User profile image -->
    <div class="profile-img">
        {{--Replace with User image here--}}
        <img src="{{IMG}}/users/1.jpg" alt="user"/>
    </div>
    <!-- User profile text -->
    <div class="profile-text">

        <a href="#"
           class="dropdown-toggle u-dropdown"
           data-toggle="dropdown"
           role="button"
           aria-haspopup="true"
           aria-expanded="true"
        >
            {{ Auth::user()->nome }}
        </a>

        <div class="dropdown-menu animated flipInY">

            <a href="#" class="dropdown-item"><i class="ti-user"></i> Meu perfil</a>
            <a href="#" class="dropdown-item"><i class="ti-wallet"></i> Meu saldo</a>
            <a href="#" class="dropdown-item"><i class="ti-email"></i> Caixa de entrada</a>

            <div class="dropdown-divider"></div>

            <a href="#" class="dropdown-item"><i class="ti-settings"></i> Configurações</a>

            <div class="dropdown-divider"></div>

            <a href="/logout" class="dropdown-item"><i class="fa fa-power-off"></i> Sair</a>

        </div>
    </div>
</div>
