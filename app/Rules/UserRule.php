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

    public function reset($request)
    {
        $rules = [
            'email' => 'required|string|email|max:50',
        ];

        $messages = [
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function verify($request)
    {
        $rules = [
            'email' => 'required|string|email|max:50',
            'code' => 'required',
        ];

        $messages = [
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
            'code.required' => 'O código para redefinir sua senha é obrigatório',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function resetPassword($request)
    {
        $rules = [
            'email' => 'required|string|email|max:50',
            'password' => 'required|string|min:8',
        ];

        $messages = [
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
            'password.required' => 'A senha é obrigatória',
            'password.string' => 'A senha deve ser uma string',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres',
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

    public function include($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:30',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:50|unique:users',
            'position' => 'required|string',
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
            'position.required' => 'O cargo do novo usuário é obrigatório',
            'position.string' => 'O cargo do novo usuário deve ser uma string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function storeByCounter($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:30',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:50|unique:users',
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
            'position.required' => 'O cargo do novo usuário é obrigatório',
            'position.string' => 'O cargo do novo usuário deve ser uma string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function startOfficeNewUser($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:30',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:50|unique:users',
            'position' => 'required|string',
            'enterpriseId' => 'required|string',
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
            'position.required' => 'O cargo do novo usuário é obrigatório',
            'position.string' => 'O cargo do novo usuário deve ser uma string',
            'enterpriseId.required' => 'O ID da organização é obrigatório',
            'enterpriseId.string' => 'O ID da organização deve ser uma string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function updateMember($request)
    {
        $rules = [
            'id' => 'required|string|max:100',
            'name' => 'required|string|min:3|max:30',
            'email' => 'required|string|email|max:50',
            'position' => 'required|string',
        ];

        $messages = [
            'id.required' => 'O ID do membro é obrigatório',
            'id.string' => 'O ID do membro deve ser uma string',
            'id.max' => 'O ID do membro não pode ter mais de 100 caracteres',
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome não pode ter menos de 3 caracteres',
            'name.max' => 'O nome não pode ter mais de 30 caracteres',
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
            'position.required' => 'O cargo do novo usuário é obrigatório',
            'position.string' => 'O cargo do novo usuário deve ser uma string',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function updateData($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:30',
            'email' => 'required|string|email|max:50',
        ];

        $messages = [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome não pode ter menos de 3 caracteres',
            'name.max' => 'O nome não pode ter mais de 30 caracteres',
            'email.required' => 'O e-mail é obrigatório',
            'email.string' => 'O e-mail deve ser uma string',
            'email.email' => 'O e-mail deve ser um endereço de e-mail válido',
            'email.max' => 'O e-mail não pode ter mais de 50 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public function updatePassword($request)
    {
        $rules = [
            'passwordActual' => 'required|string',
            'passwordNew' => 'required|string|min:8',
        ];

        $messages = [
            'passwordActual.required' => 'A senha é obrigatória',
            'passwordActual.string' => 'A senha deve ser uma string',
            'passwordNew.required' => 'A senha é obrigatória',
            'passwordNew.string' => 'A senha deve ser uma string',
            'passwordNew.min' => 'A senha deve ter pelo menos 8 caracteres',
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
            'id' => 'required|string|exists:users,id',
        ];

        $messages = [
            'id.required' => 'O ID do usuário é obrigatório',
            'id.string' => 'O ID do usuário deve ser uma string',
            'id.exists' => 'O ID do usuário não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
