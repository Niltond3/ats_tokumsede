<?php

namespace App\Actions\Pedidos;

use App\Traits\OrderProcessing;
use App\Traits\DateProcessing;
use App\Services\FCMNotificationService;

abstract class BasePedidoAction
{
    use OrderProcessing, DateProcessing;

    protected FCMNotificationService $fcmService;

    public function __construct(FCMNotificationService $fcmService)
    {
        $this->fcmService = $fcmService;
    }
}
