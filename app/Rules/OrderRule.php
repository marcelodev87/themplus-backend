<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderRule
{
    public function create($request)
    {
        $rules = [
            'enterpriseId' => 'required|string|min:1|max:2000|exists:enterprises,id',
            'description' => 'nullable|string|min:1|max:5000',
        ];

        $messages = [
            'enterpriseId.required' => 'A descrição da solicitação é obrigatório',
            'enterpriseId.string' => 'A descrição da solicitação deve ser uma string',
            'enterpriseId.min' => 'A descrição da solicitação não pode ter menos de 1 caracteres',
            'enterpriseId.max' => 'A descrição da solicitação não pode ter mais de 2000 caracteres',
            'enterpriseId.exists' => 'O ID da organização não existe',
            'description.string' => 'A descrição da solicitação deve ser uma string',
            'description.min' => 'A descrição da solicitação não pode ter menos de 1 caracteres',
            'description.max' => 'A descrição da solicitação não pode ter mais de 5000 caracteres',
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
            'id' => 'required|string|min:1|max:2000|exists:orders,id',
            'description' => 'nullable|string|min:1|max:5000',
        ];

        $messages = [
            'id.required' => 'A descrição da solicitação é obrigatório',
            'id.string' => 'A descrição da solicitação deve ser uma string',
            'id.min' => 'A descrição da solicitação não pode ter menos de 1 caracteres',
            'id.max' => 'A descrição da solicitação não pode ter mais de 2000 caracteres',
            'id.exists' => 'O ID da solicitação não existe',
            'description.string' => 'A descrição da solicitação deve ser uma string',
            'description.min' => 'A descrição da solicitação não pode ter menos de 1 caracteres',
            'description.max' => 'A descrição da solicitação não pode ter mais de 5000 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function actionClient($request)
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
            'id' => 'required|string',
        ];

        $messages = [
            'id.required' => 'O ID da solicitação é obrigatória',
            'id.string' => 'O ID da solicitação deve ser uma string',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }

    public function deleteBond($id)
    {
        $rules = [
            'id' => 'required|string',
        ];

        $messages = [
            'id.required' => 'O ID do cliente é obrigatória',
            'id.string' => 'O ID do cliente deve ser uma string',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
