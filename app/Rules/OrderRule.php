<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderRule
{
    public function create($request)
    {
        $rules = [
            'userId' => 'required|string|min:1|max:2000|exists:users,id',
        ];

        $messages = [
            'userId.required' => 'A descrição da alerta é obrigatório',
            'userId.string' => 'A descrição da alerta deve ser uma string',
            'userId.min' => 'A descrição da alerta não pode ter menos de 1 caracteres',
            'userId.max' => 'A descrição da alerta não pode ter mais de 2000 caracteres',
            'id.exists' => 'O ID do usuário não existe',
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
            'id' => 'required|string|exists:orders,id',
            'status' => 'required|string',
        ];

        $messages = [
            'id.required' => 'O ID da movimentação é obrigatório',
            'id.string' => 'O ID da movimentação deve ser uma string',
            'id.exists' => 'O ID da solicitação não existe',
            'status.required' => 'O tipo de movimentação é obrigatório',
            'status.string' => 'O tipo de movimentação deve ser uma string',
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
            'id' => 'required|string|exists:alerts,id',
        ];

        $messages = [
            'id.required' => 'O ID da solicitação é obrigatória',
            'id.string' => 'O ID da solicitação deve ser uma string',
            'id.exists' => 'O ID da solicitação não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
