<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreditCardRule
{
    public function create($request)
    {
        $currentYear = Carbon::now('America/Sao_Paulo')->year;
        $rules = [
            'subscriptionID' => 'required|exists:subscriptions,id',
            'creditCard' => 'required|array',
            'creditCard.holderName' => 'required|string|max:30',
            'creditCard.number' => 'required|string|max:16',
            'creditCard.expiryMonth' => 'required|numeric|max:12|min:1',
            'creditCard.expiryYear' => "required|numeric|min:{$currentYear}",
            'creditCard.ccv' => 'required|string|max:3',
            'creditCardHolderInfo' => 'required|array',
            'creditCardHolderInfo.name' => 'required|string|max:100',
            'creditCardHolderInfo.email' => 'required|email|max:100',
            'creditCardHolderInfo.cpfCnpj' => ['required', 'string', 'regex:/^\d{11}$|^\d{14}$/', new CpfCnpjRule],
            'creditCardHolderInfo.postalCode' => 'required|string',
            'creditCardHolderInfo.addressNumber' => 'required|string|max:16',
            'creditCardHolderInfo.addressComplement' => 'string|nullable|max:100',
            'creditCardHolderInfo.phone' => 'required|string|max:20',
        ];

        $messages = [
            'subscriptionID.required' => 'O ID da assinatura é obrigatória.',
            'subscriptionID.exists' => 'O ID da assinatura informado não existe.',
            'creditCard.required' => 'Os dados do cartão são obrigatórios.',
            'creditCard.array' => 'Os dados do cartão devem ser um array.',
            'creditCard.holderName.required' => 'O nome do titular do cartão é obrigatório.',
            'creditCard.holderName.string' => 'O nome do titular do cartão deve ser um texto.',
            'creditCard.holderName.max' => 'O nome do titular não pode ter mais que 30 caracteres.',
            'creditCard.number.required' => 'O número do cartão é obrigatório.',
            'creditCard.number.string' => 'O número do cartão deve ser um texto.',
            'creditCard.number.max' => 'O número do cartão não pode ter mais que 16 caracteres.',
            'creditCard.expiryMonth.required' => 'O mês de expiração é obrigatório.',
            'creditCard.expiryMonth.string' => 'O mês de expiração deve ser um texto.',
            'creditCard.expiryMonth.max' => 'O mês de expiração não pode ter mais que 12.',
            'creditCard.expiryMonth.min' => 'O mês de expiração não pode ser menor que 1.',
            'creditCard.expiryYear.required' => 'O ano de expiração é obrigatório.',
            'creditCard.expiryYear.string' => 'O ano de expiração deve ser um texto.',
            'creditCard.expiryYear.min' => 'O ano de expiração não pode ser menor que o ano atual.',
            'creditCard.ccv.required' => 'O código de segurança (CCV) é obrigatório.',
            'creditCard.ccv.string' => 'O código de segurança deve ser um texto.',
            'creditCard.ccv.max' => 'O código de segurança não pode ter mais que 3 caracteres.',
            'creditCardHolderInfo.required' => 'As informações do titular são obrigatórias.',
            'creditCardHolderInfo.array' => 'As informações do titular devem ser um array.',
            'creditCardHolderInfo.name.required' => 'O nome do titular é obrigatório.',
            'creditCardHolderInfo.name.string' => 'O nome do titular deve ser um texto.',
            'creditCardHolderInfo.name.max' => 'O nome do titular não pode ter mais que 100 caracteres.',
            'creditCardHolderInfo.email.required' => 'O email do titular é obrigatório.',
            'creditCardHolderInfo.email.email' => 'O email do titular deve ser válido.',
            'creditCardHolderInfo.email.max' => 'O email do titular não pode ter mais que 100 caracteres.',
            'creditCardHolderInfo.cpfCnpj.required' => 'O CPF/CNPJ é obrigatório.',
            'creditCardHolderInfo.cpfCnpj.string' => 'O CPF/CNPJ deve ser um texto.',
            'creditCardHolderInfo.cpfCnpj.regex' => 'O CPF/CNPJ deve conter exatamente 11 ou 14 dígitos numéricos.',
            'creditCardHolderInfo.postalCode.required' => 'O CEP é obrigatório.',
            'creditCardHolderInfo.postalCode.string' => 'O CEP deve ser um texto.',
            'creditCardHolderInfo.addressNumber.required' => 'O número do endereço é obrigatório.',
            'creditCardHolderInfo.addressNumber.string' => 'O número do endereço deve ser um texto.',
            'creditCardHolderInfo.addressNumber.max' => 'O número do endereço não pode ter mais que 16 caracteres.',
            'creditCardHolderInfo.addressComplement.string' => 'O complemento do endereço deve ser um texto.',
            'creditCardHolderInfo.addressComplement.max' => 'O complemento do endereço não pode ter mais que 100 caracteres.',
            'creditCardHolderInfo.phone.required' => 'O telefone é obrigatório.',
            'creditCardHolderInfo.phone.string' => 'O telefone deve ser um texto.',
            'creditCardHolderInfo.phone.max' => 'O telefone não pode ter mais que 20 caracteres.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
