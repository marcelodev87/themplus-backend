<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FeedbackRule
{
    public function create($request)
    {
        $rules = [
            'message' => 'required|string|min:1|max:5000',
        ];

        $messages = [
            'message.required' => 'A mensagem nome é obrigatória',
            'message.string' => 'A mensagem deve ser uma string',
            'message.min' => 'A mensagem não pode ter menos de 1 caracteres',
            'message.max' => 'A mensagem não pode ter mais de 5000 caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
