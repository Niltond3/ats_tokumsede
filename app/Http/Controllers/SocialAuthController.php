<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            Debugbar::info($socialUser);
            $user = Cliente::where('provider_id', $socialUser->id)
                          ->where('provider', $provider)
                          ->first();

            if (!$user) {
                $user = Cliente::create([
                    'nome' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'status' => Cliente::ATIVO
                ]);
            }

            Auth::guard('cliente')->login($user);

            return redirect()->intended(route('cliente.dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('cliente.login')
                           ->withErrors(['error' => 'Erro ao realizar login social']);
        }
    }
    // Método para autenticação via token recebido do front-end
    public function callbackToken(Request $request)
    {
        $token = $request->token;
        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]); // Certifique-se de definir GOOGLE_CLIENT_ID no .env

        $payload = $client->verifyIdToken($token);
        if ($payload) {
            // Verifica se o usuário já existe
            $user = Cliente::where('provider_id', $payload['sub'])
                           ->where('provider', 'google')
                           ->first();
            if (!$user) {
                // Cria o usuário utilizando os dados retornados do Google
                $user = Cliente::create([
                    'nome'        => $payload['name'],
                    'email'       => $payload['email'],
                    'provider'    => 'google',
                    'provider_id' => $payload['sub'],
                    'avatar'      => $payload['picture'],
                    'status'      => Cliente::ATIVO,
                ]);
            }

            // Realiza o login com o guard apropriado (no exemplo, 'cliente')
            Auth::guard('cliente')->login($user);

            return response()->json(['message' => 'Usuário autenticado', 'user' => $user]);
        }
        return response()->json(['message' => 'Token inválido'], 403);
    }
}
