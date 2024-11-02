<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FinancialMovementRule
{
    public function create($request)
    {
        $rules = [
            'type' => 'required|string',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'category_id' => 'required|string',
            'account_id' => 'required|string',
        ];

        $messages = [
            'type.required' => 'O tipo de movimentação é obrigatório',
            'type.string' => 'O tipo de movimentação deve ser uma string',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.regex' => 'O valor deve ter no máximo duas casas decimais',
            'category_id.required' => 'O ID da categoria é obrigatório',
            'category_id.string' => 'O ID da categoria deve ser uma string',
            'account_id.required' => 'O ID da conta é obrigatório',
            'account_id.string' => 'O ID da conta deve ser uma string',
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
            'type' => 'required|string',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'category_id' => 'required|string',
            'account_id' => 'required|string',
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
            'category_id.required' => 'O ID da categoria é obrigatório',
            'category_id.string' => 'O ID da categoria deve ser uma string',
            'account_id.required' => 'O ID da conta é obrigatório',
            'account_id.string' => 'O ID da conta deve ser uma string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
