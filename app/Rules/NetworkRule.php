<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NetworkRule
{
    public function create($request)
    {
        $rules = [
            'name'               => 'required|string|min:2|max:50',
            'member_id'          => 'required|exists:members,id',
            'congregation_id'    => 'required|exists:congregations,id',
        ];

        $messages = [
            'name.required'          => 'O nome do cargo é obrigatório',
            'name.string'            => 'O nome deve ser uma string',
            'name.min'               => 'O nome do cargo não pode ter menos de 2 caracteres',
            'name.max'               => 'O nome do cargo não pode ter mais de 50 caracteres',
            'member_id.required'     => 'O membro é obrigatório',
            'member_id.exists'       => 'O membro selecionado não existe',
            'congregation_id.required' => 'A congregação é obrigatória',
            'congregation_id.exists'   => 'A congregação selecionada não existe',
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
            'member_id'          => 'required|exists:members,id',
            'congregation_id'    => 'required|exists:congregations,id',
        ];

        $messages = [
            
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
