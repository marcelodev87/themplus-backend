<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserRule
{
    public function login($request)
    {
        $rules = [
            'email' => 'required|string|email|max:50',
            'password' => 'required|string',
        ];

        $messages = [
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
            'password.required' => 'A senha é obrigatória',
            'password.string' => 'A senha deve ser uma string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:30',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:50|unique:users',
            'nameEnterprise' => 'required|string|min:3|max:30',
        ];

        $messages = [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome não pode ter menos de 3 caracteres',
            'name.max' => 'O nome não pode ter mais de 30 caracteres',
            'password.required' => 'A senha é obrigatória',
            'password.string' => 'A senha deve ser uma string',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres',
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
            'email.unique' => 'Este e-mail já está registrado',
            'nameEnterprise.required' => 'O nome da empresa é obrigatório',
            'nameEnterprise.string' => 'O nome da empresa deve ser uma string',
            'nameEnterprise.min' => 'O nome da empresa não pode ter menos de 3 caracteres',
            'nameEnterprise.max' => 'O nome da empresa não pode ter mais de 30 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
