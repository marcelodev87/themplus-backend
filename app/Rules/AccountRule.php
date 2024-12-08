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

    public function createTransfer($request)
    {
        $rules = [
            'accountOut' => 'required|string',
            'accountEntry' => 'required|string',
            'date' => 'required|date_format:d/m/Y',
            'value' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ];

        $messages = [
            'accountOut.required' => 'O ID da conta de saída é obrigatória',
            'accountOut.string' => 'O ID da conta de saída deve ser uma string',
            'accountEntry.required' => 'O ID da conta de entrada é obrigatória',
            'accountEntry.string' => 'O ID da conta de entrada deve ser uma string',
            'value.required' => 'O valor é obrigatório',
            'value.regex' => 'O valor deve ter no máximo duas casas decimais',
            'date.required' => 'A data de transferência é obrigatória',
            'date.date_format' => 'A data de transferência deve estar no formato DD-MM-YYYY',
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

    public function delete($id)
    {
        $rules = [
            'id' => 'required|string|exists:accounts,id',
        ];

        $messages = [
            'id.required' => 'O ID da conta é obrigatória',
            'id.string' => 'O ID da conta deve ser uma string',
            'id.exists' => 'O ID da conta não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
