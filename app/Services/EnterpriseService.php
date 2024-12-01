<?php

namespace App\Services;

use App\Helpers\UserHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\UserRepository;
use App\Rules\EnterpriseRule;

class EnterpriseService
{
    protected $rule;

    protected $repository;

    protected $userRepository;

    public function __construct(
        EnterpriseRule $rule,
        EnterpriseRepository $repository,
        UserRepository $userRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
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
