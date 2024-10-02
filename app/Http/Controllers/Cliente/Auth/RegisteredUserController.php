<?php

namespace App\Http\Controllers\Cliente\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Cliente/Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => 'required|string|max:50',
            'dddTelefone' => 'required|string|max:2',
            'telefone' => 'required|string|max:9|unique:'.User::class,
            'sexo' => 'nullable|int',
            'dataNascimento' => 'nullable|date',
            'email' => 'required|string|lowercase|email|max:255|:',
            'senha' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipoPessoa' => 'required|string',
            'cpf' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'outrosContatos' => 'nullable|string',
        ]);

        $user = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'dddTelefone' => $request->dddTelefone,
            'telefone' => $request->telefone,
            'sexo' => $request->sexo,
            'dataNascimento' => $request->dataNascimento,
            'tipoPessoa' => $request->tipoPessoa,
            'cpf' => $request->cpf,
            'cnpj' => $request->cnpj,
            'outrosContatos' => $request->outrosContatos,

        ]);

        event(new Registered($user));

        Auth::guard('cliente')->login($user);

        return redirect(route('cliente.dashboard', absolute: false));
    }
}
