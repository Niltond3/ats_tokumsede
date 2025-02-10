<?php

namespace App\Services;

class NotificationService
{
    public function gcmSend($regId, $clientId, $orderId, $status, $returnMessage, $origin, $notify)
    {
        $fields = [
            'registration_ids' => [$regId],
            'data' => [
                'clientId' => $clientId,
                'orderId' => $orderId,
                'status' => $status,
                'returnMessage' => $returnMessage,
                'origin' => $origin,
                'notify' => $notify
            ]
        ];

        $headers = [
            'Authorization: key=' . config('services.fcm.key'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
