<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:30',
        ];

        $messages = [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome da categoria não pode ter menos de 3 caracteres',
            'name.max' => 'O nome da categoria não pode ter mais de 30 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
