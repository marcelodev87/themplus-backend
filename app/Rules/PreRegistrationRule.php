<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PreRegistrationRule
{
    public function create($request)
    {
         $rules = [
            'enterprise_id' => 'required|exists:enterprises,id',
            'name'  => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'role'  => 'nullable|string|min:2|max:100',
            'phone' => 'required|string|min:10|max:20',
            'description' => 'nullable|string|max:500',
            'relationship' => 'nullable|array',
            'relationship.*.member' => 'required|string',
            'relationship.*.kinship'  => 'required|string|min:2|max:100',
        ];

        $messages = [
            'enterprise_id.required' => 'O ID da empresa é obrigatório',
            'enterprise_id.exists'   => 'A empresa informada não existe',
            'name.required'  => 'O nome é obrigatório',
            'name.string'   => 'O nome deve ser um texto válido',
            'name.min'      => 'O nome deve ter no mínimo 3 caracteres',
            'name.max'      => 'O nome não pode ter mais de 255 caracteres',

            'email.required' => 'O e-mail é obrigatório',
            'email.email'    => 'Informe um e-mail válido',
            'email.max'      => 'O e-mail não pode ter mais de 255 caracteres',

            'role.string'   => 'O cargo deve ser um texto válido',
            'role.min'      => 'O cargo deve ter no mínimo 2 caracteres',
            'role.max'      => 'O cargo não pode ter mais de 100 caracteres',

            'phone.required' => 'O telefone é obrigatório',
            'phone.string'   => 'O telefone deve ser um texto válido',
            'phone.min'      => 'O telefone deve conter ao menos 10 dígitos',
            'phone.max'      => 'O telefone não pode ter mais de 20 caracteres',

            'description.string' => 'A descrição deve ser um texto válido',
            'description.max'    => 'A descrição pode ter no máximo 500 caracteres',

            'relationship.array' => 'O campo de vínculos deve ser uma lista',
            'relationship.*.member.required' => 'O membro é obrigatório',
            'relationship.*.member.string' => 'O membro deve ser um texto válido',
            'relationship.*.kinship.required'  => 'O grau de parentesco é obrigatório',
            'relationship.*.kinship.string'    => 'O grau de parentesco deve ser um texto válido',
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
