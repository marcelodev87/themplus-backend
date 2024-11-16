<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AlertRule
{
    public function create($request)
    {
        $rules = [
            'description' => 'required|string|min:1|max:2000',
        ];

        $messages = [
            'description.required' => 'A descrição da alerta é obrigatório',
            'description.string' => 'A descrição da alerta deve ser uma string',
            'description.min' => 'A descrição da alerta não pode ter menos de 1 caracteres',
            'description.max' => 'A descrição da alerta não pode ter mais de 2000 caracteres',
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
            'description' => 'required|string|min:1|max:2000',
        ];

        $messages = [
            'id.required' => 'O ID da alerta é obrigatório',
            'id.string' => 'O ID da alerta deve ser uma string',
            'id.max' => 'O ID da alerta não pode ter mais de 100 caracteres',
            'description.required' => 'A descrição da alerta é obrigatório',
            'description.string' => 'A descrição da alerta deve ser uma string',
            'description.min' => 'A descrição da alerta não pode ter menos de 1 caracteres',
            'description.max' => 'A descrição da alerta não pode ter mais de 2000 caracteres',
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
