<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NetworkRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:2|max:50',
            'memberID' => 'nullable|exists:members,id',
            'congregationID' => 'nullable|exists:congregations,id',
        ];

        $messages = [
            'name.required' => 'O nome do cargo é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome do cargo não pode ter menos de 2 caracteres',
            'name.max' => 'O nome do cargo não pode ter mais de 50 caracteres',
            'memberID.exists' => 'O membro selecionado não existe',
            'congregationID.exists' => 'A congregação selecionada não existe',
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
            'id' => 'required|exists:networks,id',
            'name' => 'required|string|min:2|max:50',
            'memberID' => 'nullable|exists:members,id',
            'congregationID' => 'nullable|exists:congregations,id',
        ];

        $messages = [
            'name.required' => 'O nome do cargo é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome do cargo não pode ter menos de 2 caracteres',
            'name.max' => 'O nome do cargo não pode ter mais de 50 caracteres',
            'memberID.exists' => 'O membro selecionado não existe',
            'congregationID.exists' => 'A congregação selecionada não existe',
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
            'id' => 'required|exists:networks,id',
        ];

        $messages = [
            'id.required' => 'O ID da rede é obrigatória',
            'id.exists' => 'O ID da rede não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
