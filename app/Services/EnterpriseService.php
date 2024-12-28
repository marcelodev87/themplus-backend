<?php

namespace App\Services;

use App\Helpers\EnterpriseHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Rules\EnterpriseRule;

class EnterpriseService
{
    protected $rule;

    protected $repository;

    protected $userRepository;

    protected $subscriptionRepository;

    public function __construct(
        EnterpriseRule $rule,
        EnterpriseRepository $repository,
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function createOffice($request)
    {
        $this->rule->createOffice($request);

        $subscription = $this->subscriptionRepository->findByName('free');

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
            'created_by' => $request->user()->enterprise_id,
            'subscription_id' => $subscription->id,
        ];

        return $this->repository->createOffice($data);
    }

    public function update($request)
    {
        $this->rule->update($request);

        EnterpriseHelper::existsEnterpriseCpfOrCnpj($request);

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
