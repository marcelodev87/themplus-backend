<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberChurchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
         $maritalMap = [
            'single' => 'Solteiro(a)',
            'married' => 'Casado(a)',
            'divorced' => 'Divorciado(a)',
            'widowed' => 'Viúvo(a)',
            'separated' => 'Separado(a) judicialmente',
            'civil_union' => 'União estável',
            null => null,
        ];

        $educationMap = [
            'illiterate' => 'Analfabeto',
            'elementary_incomplete' => 'Ensino Fundamental Incompleto',
            'elementary_complete' => 'Ensino Fundamental Completo',
            'high_school_incomplete' => 'Ensino Médio Incompleto',
            'high_school_complete' => 'Ensino Médio Completo',
            'technical' => 'Ensino Técnico',
            'higher_education_incomplete' => 'Ensino Superior Incompleto',
            'higher_education_complete' => 'Ensino Superior Completo',
            'postgraduate' => 'Pós-graduação',
            'masters' => 'Mestrado',
            'doctorate' => 'Doutorado',
            'post_doctorate' => 'Pós-doutorado',
            null => null,
        ];

        return [
            'name' => $this->name,
            'profession' => $this->profession,
            'date_birth' => $this->date_birth,
            'naturalness' => $this->naturalness,
            'marital_status' => $maritalMap[$this->marital_status] ?? null,
            'education' => $educationMap[$this->education] ?? null,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'email_professional' => $this->email_professional,
            'phone' => $this->phone,
            'phone_professional' => $this->phone_professional,
            'cep' => $this->cep,
            'uf' => $this->uf,
            'address' => $this->address,
            'address_number' => $this->address_number,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'complement' => $this->complement,
        ];
    }
}
