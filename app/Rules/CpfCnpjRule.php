<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CpfCnpjRule implements Rule
{
    public static function normalize(string $value): string
    {
        return preg_replace('/[\.\-\s\/]/', '', strtoupper($value));
    }

    public function passes($attribute, $value)
    {
        $cleanValue = self::normalize((string) $value);

        if (strlen($cleanValue) === 11 && ctype_digit($cleanValue)) {
            return $this->validateCpf($cleanValue);
        }

        if (strlen($cleanValue) === 14) {
            return $this->validateCnpj($cleanValue);
        }

        return false;
    }

    public function message()
    {
        return 'Informe um CPF ou CNPJ válido.';
    }

    protected function validateCpf(string $cpf): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            $peso = $t + 1;

            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * $peso;
                $peso--;
            }

            $resto = $soma % 11;
            $digito = ($resto < 2) ? 0 : (11 - $resto);

            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }

    protected function validateCnpj(string $cnpj): bool
    {
        if (! preg_match('/^[A-Z0-9]{14}$/', $cnpj)) {
            return false;
        }

        if (preg_match('/^(.)\1{13}$/', $cnpj)) {
            return false;
        }

        if (! ctype_digit(substr($cnpj, 12, 2))) {
            return false;
        }

        $pesos1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;

        for ($i = 0; $i < 12; $i++) {
            $soma += $this->getWeightValue($cnpj[$i]) * $pesos1[$i];
        }

        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : (11 - $resto);

        if ((int) $cnpj[12] !== $dv1) {
            return false;
        }

        $pesos2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;

        for ($i = 0; $i < 13; $i++) {
            $soma += $this->getWeightValue($cnpj[$i]) * $pesos2[$i];
        }

        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : (11 - $resto);

        if ((int) $cnpj[13] !== $dv2) {
            return false;
        }

        return true;
    }

    private function getWeightValue(string $char): int
    {
        return ord($char) - 48;
    }
}
