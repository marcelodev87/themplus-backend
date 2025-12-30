<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CpfCnpjRule implements Rule
{
    public function passes($attribute, $value)
    {
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) === 11) {
            return $this->validateCpf($digits);
        }

        if (strlen($digits) === 14) {
            return $this->validateCnpj($digits);
        }

        return false;
    }

    public function message()
    {
        return 'Informe um CPF ou CNPJ válido.';
    }

    protected function validateCpf(string $cpf): bool
    {
        if (preg_match('/^(\\d)\\1{10}$/', $cpf)) {
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
        if (preg_match('/^(\\d)\\1{13}$/', $cnpj)) {
            return false;
        }

        $pesos1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma += (int) $cnpj[$i] * $pesos1[$i];
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : (11 - $resto);
        if ((int) $cnpj[12] !== $dv1) {
            return false;
        }

        $pesos2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < 13; $i++) {
            $soma += (int) $cnpj[$i] * $pesos2[$i];
        }
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : (11 - $resto);
        if ((int) $cnpj[13] !== $dv2) {
            return false;
        }

        return true;
    }
}
