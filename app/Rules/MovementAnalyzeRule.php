<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MovementAnalyzeRule
{
    public function store($request)
    {
        $rules = [
            'phone' => 'required|string',
            'type' => 'required|string',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'date' => 'required|date_format:d-m-Y',

        ];

        $messages = [
            'phone.required' => 'O número do telefone é obrigatório',
            'phone.string' => 'O número do telefone deve ser uma string',
            'type.required' => 'O tipo de movimentação é obrigatório',
            'type.string' => 'O tipo de movimentação deve ser uma string',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.regex' => 'O valor deve ter no máximo duas casas decimais',
            'date.required' => 'A data de movimentação é obrigatória',
            'date.date_format' => 'A data de movimentação deve estar no formato DD-MM-YYYY',
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
            'id' => 'required|string',
            'phone' => 'required|string',
            'type' => 'required|string',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'date' => 'required|date_format:d-m-Y',

        ];

        $messages = [
            'id.required' => 'O ID da pré-movimentação é obrigatório',
            'id.string' => 'O ID da pré-movimentação deve ser uma string',
            'phone.required' => 'O número do telefone é obrigatório',
            'phone.string' => 'O número do telefone deve ser uma string',
            'type.required' => 'O tipo de movimentação é obrigatório',
            'type.string' => 'O tipo de movimentação deve ser uma string',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.regex' => 'O valor deve ter no máximo duas casas decimais',
            'date.required' => 'A data de movimentação é obrigatória',
            'date.date_format' => 'A data de movimentação deve estar no formato DD-MM-YYYY',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function finalize($request)
    {
        $rules = [
            'id' => 'required|string',
            'type' => 'required|string',
            'value' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'date' => 'required|date_format:Y-m-d',
            'category' => 'required|string',
            'account' => 'required|string',
        ];

        $messages = [
            'id.required' => 'O ID da pré-movimentação é obrigatório',
            'id.string' => 'O ID da pré-movimentação deve ser uma string',
            'type.required' => 'O tipo da pré-movimentação é obrigatório',
            'type.string' => 'O tipo da pré-movimentação deve ser uma string',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.regex' => 'O valor deve ter no máximo duas casas decimais',
            'date.required' => 'A data da pré-movimentação é obrigatória',
            'date.date_format' => 'A data da pré-movimentação deve estar no formato YYYY-MM-DD',
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
            'id' => 'required|string|exists:movements_analyze,id',
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
