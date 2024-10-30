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
            'type' => 'required|string',
        ];

        $messages = [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome da categoria não pode ter menos de 3 caracteres',
            'name.max' => 'O nome da categoria não pode ter mais de 30 caracteres',
            'type.required' => 'O tipo de movimentação da categoria é obrigatório',
            'type.string' => 'O tipo de movimentação deve ser uma string',
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
            'name' => 'required|string|min:3|max:30',
            'type' => 'required|string',
            'enterpriseId' => 'required|string|max:100',
        ];

        $messages = [
            'id.required' => 'O id da categoria é obrigatório',
            'id.string' => 'O id deve ser uma string',
            'id.max' => 'O id da categoria não pode ter mais de 100 caracteres',
            'name.required' => 'O nome da categoria é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome da categoria não pode ter menos de 3 caracteres',
            'name.max' => 'O nome da categoria não pode ter mais de 30 caracteres',
            'type.required' => 'O tipo de movimentação da categoria é obrigatório',
            'type.string' => 'O tipo de movimentação deve ser uma string',
            'enterpriseId.required' => 'O ID da empresa é obrigatório',
            'enterpriseId.string' => 'O ID da empresa deve ser uma string',
            'enterpriseId.max' => 'O ID da empresa não pode ter mais de 100 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
