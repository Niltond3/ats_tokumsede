<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use \Barryvdh\Debugbar\Facades\Debugbar;

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
        try {
            $notification = [
                'title' => "Pedido {$pedido->id} - {$cliente->nome}",
                'body' => "{$endereco->logradouro} {$endereco->numero}, {$endereco->bairro} - {$endereco->cidade}/{$endereco->estado}",
                'tag' => $pedido->id,
                'icon' => '/images/logo-icon.png',
                'click_action' => 'https://tks.tokumsede.com.br'
            ];

            $tokens = [];
            foreach ($administradores as $admin) {
                $tokens = array_merge($tokens, array_filter([$admin->token_fcm, $admin->token_fcm_mobile]));
            }

            if (empty($tokens)) {
                Debugbar::warning('Nenhum token FCM válido encontrado para envio de notificação');
                return ['sucesso' => false, 'mensagem' => 'Nenhum dispositivo disponível para envio de notificação'];
            }

            return $this->sendBatchNotification($tokens, $notification);

        } catch (\Exception $e) {
            Debugbar::error('Erro ao enviar notificação FCM: ' . $e->getMessage());
            return ['sucesso' => false, 'mensagem' => 'Erro ao processar envio de notificação'];
        }
    }

    private function sendBatchNotification(array $tokens, array $notification)
    {
        try {
            $chunks = array_chunk($tokens, 1000);
            $resultados = [];

            foreach ($chunks as $chunk) {
                $fields = [
                    'priority' => 'high',
                    'registration_ids' => $chunk,
                    'notification' => $notification
                ];

                $resultado = $this->sendRequest($fields);
                $resultados[] = $resultado;
            }

            return ['sucesso' => true, 'mensagem' => 'Notificações enviadas com sucesso', 'detalhes' => $resultados];

        } catch (\Exception $e) {
            return ['sucesso' => false, 'mensagem' => 'Falha no envio das notificações'];
        }
    }

    private function sendRequest(array $fields)
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->post(self::FCM_URL, $fields);

            if (!$response->successful()) {
                return ['sucesso' => false, 'mensagem' => 'Erro na comunicação com servidor FCM'];
            }

            return $response->json();

        } catch (\Exception $e) {
            throw new \Exception('Erro na comunicação com servidor de notificações');
        }
    }
}
