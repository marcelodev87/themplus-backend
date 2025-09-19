<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CellRule
{
    public function create($request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'dateFoundation' => 'nullable|date_format:d-m-Y',
            'dateEnd' => 'nullable|date_format:d-m-Y|after_or_equal:dateFoundation',
            'networkID' => 'nullable|exists:networks,id',
            'leaderID' => 'nullable|exists:members,id',
            'hostID' => 'nullable|exists:members,id',
            'active' => 'required|in:1,0',
            'location' => 'nullable|string|max:255',
            'dayWeek' => 'required|numeric|min:1|max:7',
            'frequency' => 'required|string|max:20',
            'time' => 'nullable|string',
            'cep' => 'nullable|string|size:9',
            'uf' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'addressNumber' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'complement' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'members.*.id' => 'required_with:members|exists:members,id',
        ];

        $messages = [
            'name.required' => 'O nome da célula é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'dateFoundation.date_format' => 'A data de fundação deve estar no formato DD-MM-AAAA.',
            'dateEnd.date_format' => 'A data de encerramento deve estar no formato DD-MM-AAAA.',
            'dateEnd.after_or_equal' => 'A data de encerramento deve ser igual ou posterior à data de fundação.',
            'networkID.exists' => 'A rede selecionada é inválida.',
            'leaderID.exists' => 'O dirigente selecionado é inválida.',
            'hostID.exists' => 'O anfitrião selecionado é inválido.',
            'active.required' => 'O campo ativo é obrigatório',
            'active.in' => 'O campo ativo deve ser 1 ou 0.',
            'location.string' => 'A localização deve ser um texto.',
            'location.max' => 'A localização não pode ter mais de 255 caracteres.',
            'dayWeek.required' => 'O dia da semana é obrigatório.',
            'dayWeek.numeric' => 'O dia da semana deve ser numérico.',
            'dayWeek.min' => 'O dia da semana deve o valor de no minímo 1',
            'dayWeek.max' => 'O dia da semana deve o valor de no máximo 7',
            'frequency.required' => 'A frequência é obrigatória.',
            'frequency.string' => 'A frequência deve ser um texto.',
            'frequency.max' => 'A frequência não pode ter mais de 20 caracteres.',
            'time.string' => 'O horário deve ser um texto.',
            'cep.string' => 'O CEP deve ser um texto.',
            'cep.size' => 'O CEP deve ter 9 caracteres (com hífen).',
            'uf.string' => 'A UF deve ser um texto.',
            'address.string' => 'O endereço deve ser um texto.',
            'address.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'addressNumber.string' => 'O número do endereço deve ser um texto.',
            'addressNumber.max' => 'O número do endereço não pode ter mais de 20 caracteres.',
            'neighborhood.string' => 'O bairro deve ser um texto.',
            'neighborhood.max' => 'O bairro não pode ter mais de 255 caracteres.',
            'city.string' => 'A cidade deve ser um texto.',
            'city.max' => 'A cidade não pode ter mais de 255 caracteres.',
            'complement.string' => 'O complemento deve ser um texto.',
            'complement.max' => 'O complemento não pode ter mais de 255 caracteres.',
            'members.array' => 'Os membros devem ser enviados em formato de array.',
            'members.*.id.required_with' => 'Cada membro deve ter um ID.',
            'members.*.id.exists' => 'Um ou mais membros selecionados são inválidos.',
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
            'id' => 'required|exists:cells,id',
            'name' => 'required|string|min:3|max:255',
            'dateFoundation' => 'nullable|date_format:d-m-Y',
            'dateEnd' => 'nullable|date_format:d-m-Y|after_or_equal:dateFoundation',
            'networkID' => 'nullable|exists:networks,id',
            'leaderID' => 'nullable|exists:members,id',
            'hostID' => 'nullable|exists:members,id',
            'active' => 'boolean',
            'location' => 'nullable|string|max:255',
            'dayWeek' => 'required|numeric|min:1|max:7',
            'frequency' => 'required|string|max:20',
            'time' => 'nullable|string',
            'cep' => 'nullable|string|size:9',
            'uf' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'addressNumber' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'complement' => 'nullable|string|max:255',
        ];

        $messages = [
            'id.required' => 'O ID da célula é obrigatória',
            'id.exists' => 'O ID da célula não existe',
            'name.required' => 'O nome da célula é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'dateFoundation.date_format' => 'A data de fundação deve estar no formato DD-MM-AAAA.',
            'dateEnd.date_format' => 'A data de encerramento deve estar no formato DD-MM-AAAA.',
            'dateEnd.after_or_equal' => 'A data de encerramento deve ser igual ou posterior à data de fundação.',
            'networkID.exists' => 'A rede selecionada é inválida.',
            'leaderID.exists' => 'O dirigente selecionado é inválido.',
            'hostID.exists' => 'O anfitrião selecionado é inválido.',
            'active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
            'location.string' => 'A localização deve ser um texto.',
            'location.max' => 'A localização não pode ter mais de 255 caracteres.',
            'dayWeek.required' => 'O dia da semana é obrigatório.',
            'dayWeek.numeric' => 'O dia da semana deve ser numérico.',
            'dayWeek.min' => 'O dia da semana deve o valor de no minímo 1',
            'dayWeek.max' => 'O dia da semana deve o valor de no máximo 7',
            'frequency.required' => 'A frequência é obrigatória.',
            'frequency.string' => 'A frequência deve ser um texto.',
            'frequency.max' => 'A frequência não pode ter mais de 20 caracteres.',
            'time.string' => 'O horário deve ser um texto.',
            'cep.string' => 'O CEP deve ser um texto.',
            'cep.size' => 'O CEP deve ter 9 caracteres (com hífen).',
            'uf.string' => 'A UF deve ser um texto.',
            'address.string' => 'O endereço deve ser um texto.',
            'address.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'addressNumber.string' => 'O número do endereço deve ser um texto.',
            'addressNumber.max' => 'O número do endereço não pode ter mais de 20 caracteres.',
            'neighborhood.string' => 'O bairro deve ser um texto.',
            'neighborhood.max' => 'O bairro não pode ter mais de 255 caracteres.',
            'city.string' => 'A cidade deve ser um texto.',
            'city.max' => 'A cidade não pode ter mais de 255 caracteres.',
            'complement.string' => 'O complemento deve ser um texto.',
            'complement.max' => 'O complemento não pode ter mais de 255 caracteres.',
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
            'id' => 'required|string|exists:cells,id',
        ];

        $messages = [
            'id.required' => 'O ID da célula é obrigatória',
            'id.exists' => 'O ID da célula não existe',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json(['errors' => $validator->errors()], 422));
        }

        return true;
    }
}
