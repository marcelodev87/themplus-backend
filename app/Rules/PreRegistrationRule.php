<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PreRegistrationRule
{
    public function create($request)
    {
        $rules = [
            'enterpriseID' => 'required|exists:enterprises,id',
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'role' => 'nullable|string|min:2|max:100',
            'phone' => 'required|string|min:10|max:20',
            'description' => 'nullable|string|max:500',
            'profession' => 'nullable|string|min:1',
            'naturalness' => 'nullable|string|min:1',
            'education' => 'nullable|string|min:1',
            'cpf' => 'nullable|string|min:11|max:11',
            'cep' => 'nullable|string',
            'uf' => 'nullable|string|min:2|max:2',
            'address' => 'nullable|string',
            'neighborhood' => 'nullable|string',
            'city' => 'nullable|string',
            'complement' => 'nullable|string',
            'dateBirth' => 'nullable|string',
            'maritalStatus' => 'nullable|string',
            'addressNumber' => 'nullable|string',
            'dateBaptismo' => 'nullable|string',
            'relationship' => 'nullable|array',
            'relationship.*.member' => 'required|string',
            'relationship.*.kinship' => 'required|string|min:2|max:100',
        ];

        $messages = [
            'enterprise_id.required' => 'A empresa é obrigatória.',
            'enterprise_id.exists' => 'A empresa informada não foi encontrada.',
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.min' => 'O nome deve conter no mínimo 3 caracteres.',
            'name.max' => 'O nome pode conter no máximo 255 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.max' => 'O e-mail pode conter no máximo 255 caracteres.',
            'role.string' => 'O cargo deve ser um texto válido.',
            'role.min' => 'O cargo deve conter no mínimo 2 caracteres.',
            'role.max' => 'O cargo pode conter no máximo 100 caracteres.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.min' => 'O telefone deve conter no mínimo 10 dígitos.',
            'phone.max' => 'O telefone pode conter no máximo 20 caracteres.',
            'description.string' => 'A descrição deve ser um texto válido.',
            'description.max' => 'A descrição pode conter no máximo 500 caracteres.',
            'profession.string' => 'A profissão deve ser um texto válido.',
            'naturalness.string' => 'A naturalidade deve ser um texto válido.',
            'education.string' => 'A escolaridade deve ser um texto válido.',
            'cpf.string' => 'O CPF deve ser um texto válido.',
            'cpf.min' => 'O CPF deve conter exatamente 11 caracteres.',
            'cpf.max' => 'O CPF deve conter exatamente 11 caracteres.',
            'cep.string' => 'O CEP deve ser um texto válido.',
            'uf.string' => 'A UF deve ser um texto válido.',
            'uf.min' => 'A UF deve conter exatamente 2 caracteres.',
            'uf.max' => 'A UF deve conter exatamente 2 caracteres.',
            'address.string' => 'O endereço deve ser um texto válido.',
            'neighborhood.string' => 'O bairro deve ser um texto válido.',
            'city.string' => 'A cidade deve ser um texto válido.',
            'complement.string' => 'O complemento deve ser um texto válido.',
            'dateBirth.string' => 'A data de nascimento deve ser um texto válido.',
            'maritalStatus.string' => 'O estado civil deve ser um texto válido.',
            'addressNumber.string' => 'O número do endereço deve ser um texto válido.',
            'dateBaptismo.string' => 'A data de batismo deve ser um texto válido.',
            'relationship.array' => 'O campo de vínculos deve ser uma lista válida.',
            'relationship.*.member.required' => 'O membro do vínculo é obrigatório.',
            'relationship.*.member.string' => 'O membro do vínculo deve ser um texto válido.',
            'relationship.*.kinship.required' => 'O grau de parentesco é obrigatório.',
            'relationship.*.kinship.string' => 'O grau de parentesco deve ser um texto válido.',
            'relationship.*.kinship.min' => 'O grau de parentesco deve conter no mínimo 2 caracteres.',
            'relationship.*.kinship.max' => 'O grau de parentesco pode conter no máximo 100 caracteres.',
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
            'id' => 'required|string|exists:categories,id',
        ];

        $messages = [
            'id.required' => 'O ID da categoria é obrigatória',
            'id.string' => 'O ID da categoria deve ser uma string',
            'id.exists' => 'O ID da categoria não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
