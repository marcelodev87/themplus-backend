<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EnterpriseRule
{
    // public function create($request)
    // {
    //     $rules = [
    //         'name' => 'required|string|min:3|max:30',
    //         'type' => 'required|string',
    //     ];

    //     $messages = [
    //         'name.required' => 'O nome da categoria é obrigatório',
    //         'name.string' => 'O nome deve ser uma string',
    //         'name.min' => 'O nome da categoria não pode ter menos de 3 caracteres',
    //         'name.max' => 'O nome da categoria não pode ter mais de 30 caracteres',
    //         'type.required' => 'O tipo de movimentação da categoria é obrigatório',
    //         'type.string' => 'O tipo de movimentação deve ser uma string',
    //     ];

    //     $validator = Validator::make($request->all(), $rules, $messages);

    //     if ($validator->fails()) {
    //         throw new ValidationException($validator);
    //     }

    //     return true;
    // }

    public function update($request)
    {
        $rules = [
            'id' => 'required|string|max:100',
            'name' => 'required|string|min:3|max:30',
        ];

        $messages = [
            'id.required' => 'O ID da organização é obrigatório',
            'id.string' => 'O ID da organização deve ser uma string',
            'id.max' => 'O ID da organização não pode ter mais de 100 caracteres',
            'name.required' => 'O nome da organização é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome da organização não pode ter menos de 3 caracteres',
            'name.max' => 'O nome da organização não pode ter mais de 30 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
