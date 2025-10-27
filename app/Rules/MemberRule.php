<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MemberRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'profession' => 'nullable|string|min:1',
            'naturalness' => 'nullable|string|min:1',
            'education' => 'nullable|string|min:1',
            'cpf' => 'nullable|string|min:11|max:11',
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
            'cep' => 'nullable|string',
            'uf' => 'nullable|string|min:2|max:2',
            'address' => 'nullable|string',
            'neighborhood' => 'nullable|string',
            'city' => 'nullable|string',
            'complement' => 'nullable|string',
            'type' => 'nullable|string',
            'active' => 'required|in:1,0',
            'dateBirth' => 'nullable|string',
            'maritalStatus' => 'nullable|string',
            'emailProfessional' => 'nullable|string',
            'phoneProfessional' => 'nullable|string',
            'addressNumber' => 'nullable|string',
            'dateBaptismo' => 'nullable|string',
            'startDate' => 'nullable|string',
            'reasonStartDate' => 'nullable|string',
            'churchStartDate' => 'nullable|string',
            'endDate' => 'nullable|string',
            'reasonEndDate' => 'nullable|string',
            'churchEndDate' => 'nullable|string',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'ministries' => 'nullable|array',
            'ministries.*' => 'exists:ministries,id',
            // 'family' => 'nullable|array',
            // 'family.*.memberID' => 'required_with:family|exists:members,id',
            // 'family.*.statusFamily' => 'required_with:family|string',
        ];

        $messages = [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'profession.string' => 'A profissão deve ser um texto.',
            'profession.min' => 'A profissão deve ter pelo menos 1 caractere.',
            'naturalness.string' => 'A naturalidade deve ser um texto.',
            'naturalness.min' => 'A naturalidade deve ter pelo menos 1 caractere.',
            'education.string' => 'A educação deve ser um texto.',
            'education.min' => 'A educação deve ter pelo menos 1 caractere.',
            'cpf.string' => 'O CPF deve ser um texto.',
            'cpf.min' => 'O CPF deve ter exatamente 11 caracteres.',
            'cpf.max' => 'O CPF deve ter exatamente 11 caracteres.',
            'email.string' => 'O email deve ser um texto.',
            'phone.string' => 'O telefone deve ser um texto.',
            'cep.string' => 'O CEP deve ser um texto.',
            'uf.string' => 'A UF deve ser um texto.',
            'uf.min' => 'A UF deve ter exatamente 2 caracteres.',
            'uf.max' => 'A UF deve ter exatamente 2 caracteres.',
            'address.string' => 'O endereço deve ser um texto.',
            'neighborhood.string' => 'O bairro deve ser um texto.',
            'city.string' => 'A cidade deve ser um texto.',
            'complement.string' => 'O complemento deve ser um texto.',
            'type.string' => 'O tipo deve ser um texto.',
            'active.required' => 'O campo ativo é obrigatório.',
            'active.in' => 'O campo ativo deve ser 1 ou 0.',
            'dateBirth.string' => 'A data de nascimento deve ser um texto.',
            'maritalStatus.string' => 'O estado civil deve ser um texto.',
            'emailProfessional.string' => 'O email profissional deve ser um texto.',
            'phoneProfessional.string' => 'O telefone profissional deve ser um texto.',
            'addressNumber.string' => 'O número do endereço deve ser um texto.',
            'dateBaptismo.string' => 'A data de batismo deve ser um texto.',
            'startDate.string' => 'A data de início deve ser um texto.',
            'reasonStartDate.string' => 'O motivo da data de início deve ser um texto.',
            'churchStartDate.string' => 'A data de início na igreja deve ser um texto.',
            'endDate.string' => 'A data de término deve ser um texto.',
            'reasonEndDate.string' => 'O motivo da data de término deve ser um texto.',
            'churchEndDate.string' => 'A data de término na igreja deve ser um texto.',
            'roles.array' => 'O campo roles deve ser um array de IDs.',
            'roles.*.exists' => 'Um ou mais papéis selecionados são inválidos.',
            'ministries.array' => 'O campo ministries deve ser um array de IDs.',
            'ministries.*.exists' => 'Um ou mais ministros selecionados são inválidos.',
            // 'family.array' => 'Os familiares devem ser um array.',
            // 'family.*.memberID.required_with' => 'Cada familiar deve ter um ID de membro.',
            // 'family.*.memberID.exists' => 'O membro familiar selecionado é inválido.',
            // 'family.*.statusFamily.required_with' => 'Cada familiar deve ter um status familiar.',
            // 'family.*.statusFamily.string' => 'O status familiar deve ser um texto.',
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
            'id' => 'required|exists:members,id',
            'name' => 'required|string|min:3|max:255',
            'profession' => 'nullable|string|min:1',
            'naturalness' => 'nullable|string|min:1',
            'education' => 'nullable|string|min:1',
            'cpf' => 'nullable|string|min:11|max:11',
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
            'cep' => 'nullable|string',
            'uf' => 'nullable|string|min:2|max:2',
            'address' => 'nullable|string',
            'neighborhood' => 'nullable|string',
            'city' => 'nullable|string',
            'complement' => 'nullable|string',
            'type' => 'nullable|string',
            'active' => 'required|in:1,0',
            'dateBirth' => 'nullable|string',
            'maritalStatus' => 'nullable|string',
            'emailProfessional' => 'nullable|string',
            'phoneProfessional' => 'nullable|string',
            'addressNumber' => 'nullable|string',
            'dateBaptismo' => 'nullable|string',
            'startDate' => 'nullable|string',
            'reasonStartDate' => 'nullable|string',
            'churchStartDate' => 'nullable|string',
            'endDate' => 'nullable|string',
            'reasonEndDate' => 'nullable|string',
            'churchEndDate' => 'nullable|string',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'family' => 'nullable|array',
            'family.*.memberID' => 'required_with:family|exists:members,id',
            'family.*.statusFamily' => 'required_with:family|string',
        ];

        $messages = [
            'id.required' => 'O ID do membro é obrigatória',
            'id.exists' => 'O ID do membro não existe',
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'profession.string' => 'A profissão deve ser um texto.',
            'profession.min' => 'A profissão deve ter pelo menos 1 caractere.',
            'naturalness.string' => 'A naturalidade deve ser um texto.',
            'naturalness.min' => 'A naturalidade deve ter pelo menos 1 caractere.',
            'education.string' => 'A educação deve ser um texto.',
            'education.min' => 'A educação deve ter pelo menos 1 caractere.',
            'cpf.string' => 'O CPF deve ser um texto.',
            'cpf.min' => 'O CPF deve ter exatamente 11 caracteres.',
            'cpf.max' => 'O CPF deve ter exatamente 11 caracteres.',
            'email.string' => 'O email deve ser um texto.',
            'phone.string' => 'O telefone deve ser um texto.',
            'cep.string' => 'O CEP deve ser um texto.',
            'uf.string' => 'A UF deve ser um texto.',
            'uf.min' => 'A UF deve ter exatamente 2 caracteres.',
            'uf.max' => 'A UF deve ter exatamente 2 caracteres.',
            'address.string' => 'O endereço deve ser um texto.',
            'neighborhood.string' => 'O bairro deve ser um texto.',
            'city.string' => 'A cidade deve ser um texto.',
            'complement.string' => 'O complemento deve ser um texto.',
            'type.string' => 'O tipo deve ser um texto.',
            'active.required' => 'O campo ativo é obrigatório.',
            'active.in' => 'O campo ativo deve ser 1 ou 0.',
            'dateBirth.string' => 'A data de nascimento deve ser um texto.',
            'maritalStatus.string' => 'O estado civil deve ser um texto.',
            'emailProfessional.string' => 'O email profissional deve ser um texto.',
            'phoneProfessional.string' => 'O telefone profissional deve ser um texto.',
            'addressNumber.string' => 'O número do endereço deve ser um texto.',
            'dateBaptismo.string' => 'A data de batismo deve ser um texto.',
            'startDate.string' => 'A data de início deve ser um texto.',
            'reasonStartDate.string' => 'O motivo da data de início deve ser um texto.',
            'churchStartDate.string' => 'A data de início na igreja deve ser um texto.',
            'endDate.string' => 'A data de término deve ser um texto.',
            'reasonEndDate.string' => 'O motivo da data de término deve ser um texto.',
            'churchEndDate.string' => 'A data de término na igreja deve ser um texto.',
            'roles.array' => 'O campo roles deve ser um array de IDs.',
            'roles.*.exists' => 'Um ou mais papéis selecionados são inválidos.',
            'family.array' => 'Os familiares devem ser um array.',
            'family.*.memberID.required_with' => 'Cada familiar deve ter um ID de membro.',
            'family.*.memberID.exists' => 'O membro familiar selecionado é inválido.',
            'family.*.statusFamily.required_with' => 'Cada familiar deve ter um status familiar.',
            'family.*.statusFamily.string' => 'O status familiar deve ser um texto.',
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
            'id' => 'required|string|exists:members,id',
        ];

        $messages = [
            'id.required' => 'O ID do membro é obrigatória',
            'id.exists' => 'O ID do membro não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
