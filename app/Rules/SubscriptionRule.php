<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SubscriptionRule
{
    public function create($request)
    {
        $rules = [
            'subscriptionID' => 'required|exists:subscriptions,id',
        ];

        $messages = [
             'subscriptionID.required' => 'O ID da assinatura é obrigatória.',
            'subscriptionID.exists' => 'O ID da assinatura informado não existe.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
