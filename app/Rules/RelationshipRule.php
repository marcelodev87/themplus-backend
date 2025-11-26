<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RelationshipRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:1|max:30',
        ];

        $messages = [
            'name.required' => 'O nome da relação é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome da relação não pode ter menos de 1 caracteres',
            'name.max' => 'O nome da relação não pode ter mais de 30 caracteres',
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
            'name' => 'required|string|min:1|max:30',
        ];

        $messages = [
            'id.required' => 'O ID da relação é obrigatório',
            'id.string' => 'O ID da relação deve ser uma string',
            'id.max' => 'O ID da relação não pode ter mais de 100 caracteres',
            'name.required' => 'O nome da relação é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome da relação não pode ter menos de 1 caracteres',
            'name.max' => 'O nome da relação não pode ter mais de 30 caracteres',
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
            'id' => 'required|string|exists:relationships,id',
        ];

        $messages = [
            'id.required' => 'O ID da relação é obrigatória',
            'id.string' => 'O ID da relação deve ser uma string',
            'id.exists' => 'O ID da relação não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
