<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PixKeyRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (empty($value)) return true;

        return
            $this->isValidUuid($value) ||
            $this->isValidEmail($value) ||
            $this->isValidDocument($value) ||
            $this->isValidPhone($value) ||
            $this->isValidRandomKey($value);
    }

    private function isValidUuid($value): bool {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value) === 1;
    }

    private function isValidEmail($value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isValidDocument($value): bool {
        $value = preg_replace('/[^0-9]/', '', $value);
        return strlen($value) === 11 || strlen($value) === 14;
    }

    private function isValidPhone($value): bool {
        return preg_match('/^\+55\d{10,11}$/', $value) === 1;
    }

    private function isValidRandomKey($value): bool {
        return preg_match('/^[a-zA-Z0-9-._@]{8,140}$/', $value) === 1;
    }

    public function message(): string
    {
        return 'A chave PIX informada é inválida.';
    }
};
