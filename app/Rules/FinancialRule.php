<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FinancialRule
{
    public function finalize($request)
    {
        $rules = [
            'monthYear' => 'required|string|max:10',
        ];

        $messages = [
            'monthYear.required' => 'A data é obrigatório',
            'monthYear.string' => 'A data deve ser uma string',
            'monthYear.max' => 'A data não pode ter mais de 10 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
