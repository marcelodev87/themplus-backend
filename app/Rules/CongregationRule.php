<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CongregationRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:2|max:50',
            'email' => 'nullable|email',
            'cnpj' => 'nullable|string',
            'phone' => 'nullable|string',
            'cep' => 'nullable|string',
            'uf' => 'nullable|string|max:2',
            'address' => 'nullable|string',
            'neighborhood' => 'nullable|string',
            'city' => 'nullable|string',
            'complement' => 'nullable|string',
            'dateFoundation' => 'nullable|date_format:d/m/Y',
            'addressNumber' => 'nullable|string',
            'memberID' => 'nullable|exists:members,id',
        ];

        $messages = [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome deve ter no mínimo 2 caracteres',
            'name.max' => 'O nome deve ter no máximo 50 caracteres',
            'email.email' => 'O email deve ser um endereço de email válido',
            'cnpj.string' => 'O CNPJ deve ser uma string',
            'phone.string' => 'O telefone deve ser uma string',
            'cep.string' => 'O CEP deve ser uma string',
            'uf.string' => 'A UF deve ser uma string',
            'uf.max' => 'A UF deve ter no máximo 2 caracteres',
            'address.string' => 'O endereço deve ser uma string',
            'neighborhood.string' => 'O bairro deve ser uma string',
            'city.string' => 'A cidade deve ser uma string',
            'complement.string' => 'O complemento deve ser uma string',
            'dateFoundation.date_format' => 'A data de fundação deve estar no formato DD/MM/AAAA',
            'addressNumber.string' => 'O número do endereço deve ser uma string',
            'memberID.exists' => 'O membro selecionado não existe',
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
            'id' => 'required|exists:congregations,id',
            'name' => 'required|string|min:2|max:50',
            'email' => 'nullable|email',
            'cnpj' => 'nullable|string',
            'phone' => 'nullable|string',
            'cep' => 'nullable|string',
            'uf' => 'nullable|string|max:2',
            'address' => 'nullable|string',
            'neighborhood' => 'nullable|string',
            'city' => 'nullable|string',
            'complement' => 'nullable|string',
            'addressNumber' => 'nullable|string',
            'dateFoundation' => 'nullable|date_format:d/m/Y',
            'memberID' => 'nullable|exists:members,id',
        ];

        $messages = [
            'id.required' => 'O ID da congregação é obrigatório',
            'id.exists' => 'O ID da congregação selecionado não existe',
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'O nome deve ser uma string',
            'name.min' => 'O nome deve ter no mínimo 2 caracteres',
            'name.max' => 'O nome deve ter no máximo 50 caracteres',
            'email.email' => 'O email deve ser um endereço de email válido',
            'cnpj.string' => 'O CNPJ deve ser uma string',
            'phone.string' => 'O telefone deve ser uma string',
            'cep.string' => 'O CEP deve ser uma string',
            'uf.string' => 'A UF deve ser uma string',
            'uf.max' => 'A UF deve ter no máximo 2 caracteres',
            'address.string' => 'O endereço deve ser uma string',
            'neighborhood.string' => 'O bairro deve ser uma string',
            'city.string' => 'A cidade deve ser uma string',
            'complement.string' => 'O complemento deve ser uma string',
            'addressNumber.string' => 'O número do endereço deve ser uma string',
            'dateFoundation.date_format' => 'A data de fundação deve estar no formato DD/MM/AAAA',
            'memberID.exists' => 'O membro selecionado não existe',
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
            'id' => 'required|exists:congregations,id',
        ];

        $messages = [
            'id.required' => 'O ID da congregação é obrigatória',
            'id.exists' => 'O ID da congregação não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
