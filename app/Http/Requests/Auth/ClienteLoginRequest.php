<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Cliente;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Redirect;

class ClienteLoginRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'ddd'=>['required','string'],
            'telefone' => ['required', 'string'],
            'senha' => ['required', 'string'],
        ];
    }

     /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
                $this->ensureIsNotRateLimited();

                $user = Cliente::where('status','1')
                    ->where("senha", $this->string("senha"))
                    ->where("telefone", $this->string("telefone"))
                    ->first();
        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'telefone' => trans('auth.failed'),
            ]);
        }else {
            Auth::guard('cliente')->loginUsingId($user->id,$this->boolean('remember'));

            RateLimiter::clear($this->throttleKey());
        }

    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'telefone' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('telefone')).'|'.$this->ip());
    }
}
