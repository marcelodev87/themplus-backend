<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SettingsCounterRule
{
    public function update($request)
    {
        $rules = [
            'allowAddUser' => 'required|numeric|in:0,1',
            'allowEditUser' => 'required|numeric|in:0,1',
            'allowDeleteUser' => 'required|numeric|in:0,1',
            'allowEditMovement' => 'required|numeric|in:0,1',
            'allowDeleteMovement' => 'required|numeric|in:0,1',
        ];

        $messages = [
            'allowAddUser.required' => 'O campo "permitir adicionar usuário" é obrigatório.',
            'allowAddUser.numeric' => 'O campo "permitir adicionar usuário" deve ser um número.',
            'allowAddUser.in' => 'O campo "permitir adicionar usuário" deve ser 0 ou 1.',

            'allowEditUser.required' => 'O campo "permitir editar usuário" é obrigatório.',
            'allowEditUser.numeric' => 'O campo "permitir editar usuário" deve ser um número.',
            'allowEditUser.in' => 'O campo "permitir editar usuário" deve ser 0 ou 1.',

            'allowDeleteUser.required' => 'O campo "permitir deletar usuário" é obrigatório.',
            'allowDeleteUser.numeric' => 'O campo "permitir deletar usuário" deve ser um número.',
            'allowDeleteUser.in' => 'O campo "permitir deletar usuário" deve ser 0 ou 1.',

            'allowEditMovement.required' => 'O campo "permitir editar movimentação" é obrigatório.',
            'allowEditMovement.numeric' => 'O campo "permitir editar movimentação" deve ser um número.',
            'allowEditMovement.in' => 'O campo "permitir editar movimentação" deve ser 0 ou 1.',

            'allowDeleteMovement.required' => 'O campo "permitir deletar movimentação" é obrigatório.',
            'allowDeleteMovement.numeric' => 'O campo "permitir deletar movimentação" deve ser um número.',
            'allowDeleteMovement.in' => 'O campo "permitir deletar movimentação" deve ser 0 ou 1.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    // public function delete($id)
    // {
    //     $rules = [
    //         'id' => 'required|string|exists:departments,id',
    //     ];

    //     $messages = [
    //         'id.required' => 'O ID do departamento é obrigatório.',
    //         'id.string' => 'O ID do departamento deve ser uma string.',
    //         'id.exists' => 'O ID do departamento não existe.',
    //     ];

    //     $validator = Validator::make(['id' => $id], $rules, $messages);

    //     if ($validator->fails()) {
    //         throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
    //     }

    //     return true;
    // }
}
