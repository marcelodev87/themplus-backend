<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccountRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:2|max:30',
        ];

        $messages = [
            'name.required' => 'O nome da conta é obrigatória',
            'name.string' => 'O nome da conta deve ser uma string',
            'name.min' => 'O nome da conta não pode ter menos de 2 caracteres',
            'name.max' => 'O nome da conta não pode ter mais de 30 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function update($request)
    {
        $rules = [
            'id' => 'required|string|max:100',
            'name' => 'required|string|min:2|max:30',
        ];

        $messages = [
            'id.required' => 'O ID da conta é obrigatório',
            'id.string' => 'O ID da conta deve ser uma string',
            'id.max' => 'O ID da conta não pode ter mais de 100 caracteres',
            'name.required' => 'O nome da conta é obrigatório',
            'name.string' => 'O nome da conta deve ser uma string',
            'name.min' => 'O nome da conta não pode ter menos de 2 caracteres',
            'name.max' => 'O nome da conta não pode ter mais de 30 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
