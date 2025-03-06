<?php

namespace App\Services;

use App\Helpers\CategoryHelper;
use App\Helpers\EnterpriseHelper;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\SettingsCounterRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Rules\EnterpriseRule;

class EnterpriseService
{
    protected $rule;

    protected $repository;

    protected $userRepository;

    protected $subscriptionRepository;

    protected $categoryRepository;

    protected $accountRepository;

    protected $settingsCounterRepository;

    public function __construct(
        EnterpriseRule $rule,
        EnterpriseRepository $repository,
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        AccountRepository $accountRepository,
        SettingsCounterRepository $settingsCounterRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->accountRepository = $accountRepository;
        $this->settingsCounterRepository = $settingsCounterRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function createOffice($request)
    {
        $this->rule->createOffice($request);

        $subscription = $this->subscriptionRepository->findByName('free');
        $entepriseActual = $this->repository->findById($request->user()->enterprise_id);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email') ?? $entepriseActual->email,
            'cep' => $request->input('cep'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'neighborhood' => $request->input('neighborhood'),
            'address' => $request->input('address'),
            'number_address' => $request->input('numberAddress'),
            'complement' => $request->input('complement'),
            'phone' => $request->input('phone') ?? $entepriseActual->phone,
            'created_by' => $request->user()->enterprise_id,
            'subscription_id' => $subscription->id,
            'counter_enterprise_id' => $entepriseActual->counter_enterprise_id,
        ];

        if ($request->input('cnpj') === null && $request->input('cpf') === null) {
            $data['cpf'] = $entepriseActual->cpf !== null ? $entepriseActual->cpf : null;
            $data['cnpj'] = $entepriseActual->cnpj !== null ? $entepriseActual->cnpj : null;
        }

        $office = $this->repository->createOffice($data);

        $dataAccount = ['name' => 'Caixinha', 'enterprise_id' => $office->id];
        $this->accountRepository->create($dataAccount);
        $this->settingsCounterRepository->create(['enterprise_id' => $office->id]);
        CategoryHelper::createDefault($office->id);

        return $office;
    }

    public function createByCounter($request)
    {
        $this->rule->createOffice($request);

        $subscription = $this->subscriptionRepository->findByName('free');

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'cep' => $request->input('cep'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'neighborhood' => $request->input('neighborhood'),
            'address' => $request->input('address'),
            'number_address' => $request->input('number_address'),
            'complement' => $request->input('complement'),
            'phone' => $request->input('phone'),
            'subscription_id' => $subscription->id,
            'counter_enterprise_id' => $request->user()->enterprise_id,
            'cnpj' => $request->input('cnpj'),
            'cpf' => $request->input('cpf'),
        ];

        $enterprise = $this->repository->createOffice($data);

        $dataAccount = ['name' => 'Caixinha', 'enterprise_id' => $enterprise->id];
        $this->accountRepository->create($dataAccount);
        $this->settingsCounterRepository->create(['enterprise_id' => $enterprise->id]);
        CategoryHelper::createDefault($enterprise->id);

        return $enterprise;
    }

    public function update($request)
    {
        $this->rule->update($request);

        $enterprise = $this->repository->findById($request->input('id'));
        if (($request->input('cpf') && $request->input('cpf') !== $enterprise->cpf) || $request->input('cnpj') && $request->input('cnpj') !== $enterprise->cnpj) {
            EnterpriseHelper::existsEnterpriseCpfOrCnpj($request);
        }

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

    public function updateCodeFinancial($request)
    {
        $this->rule->updateCodeFinancial($request);

        $data = ['code_financial' => $request->input('code')];

        return $this->repository->update($request->input('id'), $data);
    }

    public function updateViewEnterprise($request)
    {
        $data = ['view_enterprise_id' => $request->input('viewEnterprise') ?? $request->user()->enterprise_id];

        return $this->userRepository->update($request->user()->id, $data);
    }

    public function unlink($request)
    {
        $enterprise = $this->repository->update($request->user()->enterprise_id, ['counter_enterprise_id' => null]);
        $offices = $this->repository->getAllOfficesByEnterprise($request->user()->enterprise_id);

        foreach ($offices as $office) {
            $office->update(['counter_enterprise_id' => null]);
        }

        $this->categoryRepository->removeAlert($request->user()->enterprise_id);

        return $enterprise;
    }
}
