<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FinancialMovementReceiptRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string',
            'file' => 'required',
        ];

        $messages = [
            'name.required' => 'O nome do arquivo é obrigatório',
            'name.string' => 'O nome do arquivo deve ser uma string',
            'file.required' => 'O arquivo é obrigatório',
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
            'id.required' => 'O ID do anexo é obrigatório',
            'id.string' => 'O ID do anexo deve ser uma string',
            'id.exists' => 'O ID do anexo não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
