<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MovementRule
{
    public function create($request)
    {
        $rules = [
            'type' => 'required|string',
            'programmed' => 'required|numeric',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'category' => 'required|string',
            'account' => 'required|string',
        ];

        $messages = [
            'type.required' => 'O tipo de movimentação é obrigatório',
            'type.string' => 'O tipo de movimentação deve ser uma string',
            'programmed.required' => 'O tipo de criação é obrigatório',
            'programmed.numeric' => 'O tipo de criação deve ser número',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.regex' => 'O valor deve ter no máximo duas casas decimais',
            'category.required' => 'O ID da categoria é obrigatório',
            'category.string' => 'O ID da categoria deve ser uma string',
            'account.required' => 'O ID da conta é obrigatório',
            'account.string' => 'O ID da conta deve ser uma string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function insert($request)
    {
        $rules = ['movements' => 'required|array'];
        $messages = [
            'movements.required' => 'A lista de movimentações é obrigatória',
            'movements.array' => 'A lista de movimentações deve ser um array',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function update($request)
    {
        \Log::info($request->all());

        $rules = [
            'id' => 'required|string|max:100',
            'type' => 'required|string',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'date' => 'required|date_format:d-m-Y',
            'category' => 'required|string',
            'account' => 'required|string',
        ];

        $messages = [
            'id.required' => 'O ID da movimentação é obrigatório',
            'id.string' => 'O ID da movimentação deve ser uma string',
            'id.max' => 'O ID da movimentação não pode ter mais de 100 caracteres',
            'type.required' => 'O tipo de movimentação é obrigatório',
            'type.string' => 'O tipo de movimentação deve ser uma string',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.regex' => 'O valor deve ter no máximo duas casas decimais',
            'date.required' => 'A data de movimentação é obrigatória',
            'date.date_format' => 'A data de movimentação deve estar no formato DD-MM-YYYY',
            'category.required' => 'O ID da categoria é obrigatório',
            'category.string' => 'O ID da categoria deve ser uma string',
            'account.required' => 'O ID da conta é obrigatório',
            'account.string' => 'O ID da conta deve ser uma string',
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
            'id' => 'required|string|exists:movements,id',
        ];

        $messages = [
            'id.required' => 'O ID da movimentação é obrigatória',
            'id.string' => 'O ID da movimentação deve ser uma string',
            'id.exists' => 'O ID da movimentação não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
