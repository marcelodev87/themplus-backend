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
        ];

        $messages = [
            'id.required' => 'O ID da categoria é obrigatório',
            'id.string' => 'O ID da categoria deve ser uma string',
            'id.max' => 'O ID da categoria não pode ter mais de 100 caracteres',
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

    public function delete($id)
    {
        $rules = [
            'id' => 'required|string|exists:categories,id',
        ];

        $messages = [
            'id.required' => 'O ID da categoria é obrigatória',
            'id.string' => 'O ID da categoria deve ser uma string',
            'id.exists' => 'O ID da categoria não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
