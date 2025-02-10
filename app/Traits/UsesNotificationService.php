<?php

namespace App\Traits;

use App\Services\NotificationService;

trait UsesNotificationService
{
    protected NotificationService $notificationService;

    protected function initializeNotificationService()
    {
        $this->notificationService = app(NotificationService::class);
    }

    protected function gcmSend($regId, $clientId, $orderId, $status, $returnMessage, $origin, $notify)
    {
        return $this->notificationService->gcmSend($regId, $clientId, $orderId, $status, $returnMessage, $origin, $notify);
    }
}
