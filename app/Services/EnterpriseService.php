<?php

namespace App\Services;

use App\Repositories\EnterpriseRepository;
use App\Rules\EnterpriseRule;

class EnterpriseService
{
    protected $rule;

    protected $repository;

    public function __construct(
        EnterpriseRule $rule,
        EnterpriseRepository $repository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
    }

    // public function create($request)
    // {
    //     $this->rule->create($request);

    //     $data = $request->only(['name', 'type']);
    //     $data['enterprise_id'] = $request->user()->enterprise_id;

    //     return $this->repository->create($data);
    // }

    public function update($request)
    {
        $this->rule->update($request);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'cnpj' => $request->input('cnpj'),
            'cpf' => $request->input('cpf'),
            'cep' => $request->input('cep'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'neighborhood' => $request->input('neighborhood'),
            'address' => $request->input('address'),
            'number_address' => $request->input('number_address'),
            'complement' => $request->input('complement'),
            'phone' => $request->input('phone'),

        ];

        return $this->repository->update($request->input('id'), $data);
    }
}
