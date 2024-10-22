@inject('notifications', 'navbar.notifications')

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark"
       href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
    >
        <i class="mdi mdi-message"></i>
        @if($notifications->get()->count() > 0)
            <div class="notify">
                <span class="heartbit"></span> <span class="point"></span>
            </div>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-right mailbox scale-up">
        <ul>
            <li>
                <div class="drop-title">Notifications ({{ $notifications->get()->count() }})</div>
            </li>
            <li>
                <div class="message-center">

                    @foreach($notifications->get()->slice($start = 0, $howMany = 5) as $notification)
                    <a href="#">
                        <div class="btn btn-danger btn-circle"><i class="fa fa-link"></i></div>
                        <div class="mail-contnet">
                            <h5>{{ $notification->title }}</h5>
                            <span class="mail-desc">
                                {{ str_limit($notification->body, 40) }}
                            </span>
                            <span class="time">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </li>
            <li>
                <a class="nav-link text-center" href="javascript:void(0);"> <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i> </a>
            </li>
        </ul>
    </div>
</li>