<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RoleRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:2|max:30',
        ];

        $messages = [
            'name.required' => 'O nome do cargo é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome do cargo não pode ter menos de 2 caracteres',
            'name.max' => 'O nome do cargo não pode ter mais de 30 caracteres',
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
            'id.required' => 'O ID da categoria é obrigatório',
            'id.string' => 'O ID da categoria deve ser uma string',
            'id.max' => 'O ID da categoria não pode ter mais de 100 caracteres',
            'name.required' => 'O nome do cargo é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome da categoria não pode ter menos de 2 caracteres',
            'name.max' => 'O nome da categoria não pode ter mais de 30 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function delete($id)
    {
        $rules = [
            'id' => 'required|string|exists:roles,id',
        ];

        $messages = [
            'id.required' => 'O ID do cargo é obrigatório',
            'id.string' => 'O ID do cargo deve ser uma string',
            'id.exists' => 'O ID do cargo não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
