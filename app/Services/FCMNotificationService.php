<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FCMNotificationService
{
    private const FCM_URL = 'https://fcm.googleapis.com/fcm/send';
    private $headers;

    public function __construct()
    {
        $this->headers = [
            'Authorization' => 'key=' . config('services.fcm.key'),
            'Content-Type' => 'application/json'
        ];
    }

    public function sendOrderNotification($pedido, $cliente, $endereco, $administradores)
    {
        $notification = [
            'title' => "Pedido {$pedido->id} - {$cliente->nome}",
            'body' => "{$endereco->logradouro} {$endereco->numero}, {$endereco->bairro} - {$endereco->cidade}/{$endereco->estado}",
            'tag' => $pedido->id,
            'icon' => '/images/logo-icon.png',
            'click_action' => 'https://tks.tokumsede.com.br'
        ];

        $tokens = [];
        foreach ($administradores as $admin) {
            // Adiciona os tokens válidos
            $tokens = array_merge($tokens, array_filter([$admin->token_fcm, $admin->token_fcm_mobile]));
        }
         // Envia as notificações em lote (multicast)
         $this->sendBatchNotification($tokens, $notification);
    }

    private function sendBatchNotification(array $tokens, array $notification)
    {
        // Divide os tokens em grupos de no máximo 1000
        $chunks = array_chunk($tokens, 1000);

        foreach ($chunks as $chunk) {
            $fields = [
                'priority' => 'high',
                'registration_ids' => $chunk,  // Usando 'registration_ids' para envio em lote
                'notification' => $notification
            ];

            // Envia a solicitação para o FCM
            $this->sendRequest($fields);
        }
    }

    private function sendRequest(array $fields)
    {
        $response = Http::withHeaders($this->headers)
            ->post(self::FCM_URL, $fields);

        return $response->json();
    }
}
