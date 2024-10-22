@inject('messages', 'navbar.messages')

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" id="2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
        @if($messages->get()->count() == 0)
            <i class="mdi mdi-bell"></i>
        @endif
        @if($messages->get()->count() > 0)
            <i class="mdi mdi-bell-ring"></i>
            <div class="notify">
                <span class="heartbit"></span> <span class="point"></span>
            </div>
        @endif
    </a>
    <div class="dropdown-menu mailbox dropdown-menu-right scale-up" aria-labelledby="2">
        <ul>
            <li>
                <div class="drop-title">Você tem {{ $messages->get()->count() }} novos pedidos</div>
            </li>
            <li>
                <div class="message-center">

                    @foreach($messages->get()->slice($start = 0, $howMany = 6) as $message)
                        <a href="#">
                            <div class="user-img">
                                {{--Replace with User image--}}
                                <img src="/vendor/wrappixel/material-pro/4.1.0/assets/images/users/1.jpg"
                                     alt="user"
                                     class="img-circle"
                                >
                                    <span class="profile-status online pull-right"></span>
                            </div>
                            <div class="mail-contnet">
                                <h5>Pedido {{ $message->id }}</h5>
                                <span class="mail-desc">
                                    {{ str_limit($message->idEndereco, 40) }}
                                </span>
                                <span class="time">
                                    {{ $message->horarioPedido }}
                                </span>
                            </div>
                        </a>
                    @endforeach


                </div>
            </li>
            <li>
                <a class="nav-link text-center" window.location.href="/#/listapedidos">
                    @if($messages->get()->count() > 0)
                        <span> Exibindo {{ $howMany }}. </span>
                    @endif
                    <strong> Listar todos os Pedidos</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            </li>
        </ul>
    </div>
</li>