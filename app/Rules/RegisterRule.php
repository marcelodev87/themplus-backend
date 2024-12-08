<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterRule
{
    public function create($data)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'enterprise_id' => 'required|exists:enterprises,id',
            'action' => 'required|string|max:255',
            'target' => 'required|string|max:255',
            'identification' => 'required|string|max:255',
        ];

        $messages = [
            'user_id.required' => 'O ID do usuário é obrigatório',
            'user_id.exists' => 'O ID do usuário existir na tabela Users',
            'enterprise_id.required' => 'O ID da organização é obrigatório',
            'enterprise_id.exists' => 'O ID da organização deve existir na tabela enterprises',
            'action.required' => 'A ação é obrigatória',
            'action.string' => 'A ação deve ser uma string',
            'action.max' => 'A ação não pode ter mais de 255 caracteres',
            'target.required' => 'O alvo é obrigatório',
            'target.string' => 'O alvo deve ser uma string',
            'target.max' => 'O alvo não pode ter mais de 255 caracteres',
            'identification.required' => 'A identificação é obrigatória',
            'identification.string' => 'A identificação deve ser uma string',
            'identification.max' => 'A identificação não pode ter mais de 255 caracteres',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function show($id)
    {
        $rules = [
            'id' => 'required|string|exists:registers,id',
        ];

        $messages = [
            'id.required' => 'O ID do registro é obrigatório',
            'id.string' => 'O ID do registro deve ser uma string',
            'id.exists' => 'O ID do registro não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
