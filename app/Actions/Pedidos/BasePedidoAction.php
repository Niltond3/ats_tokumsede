<?php

namespace App\Actions\Pedidos;

use App\Services\FCMNotificationService;
use App\Traits\OrderProcessing;
use App\Traits\DateProcessing;

abstract class BasePedidoAction
{
    use OrderProcessing, DateProcessing;

    protected FCMNotificationService $fcmService;

    public function __construct(FCMNotificationService $fcmService)
    {
        $this->fcmService = $fcmService;
    }
}
