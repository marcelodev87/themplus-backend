<?php

namespace App\Services;

use App\Helpers\CategoryHelper;
use App\Helpers\EnterpriseHelper;
use App\Repositories\AccountRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\EnterpriseHasCouponRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\External\CouponExternalRepository;
use App\Repositories\SettingsCounterRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Rules\CpfCnpjRule;
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

    protected $enterpriseHasCouponRepository;

    protected $couponExternalRepository;

    public function __construct(
        EnterpriseRule $rule,
        EnterpriseRepository $repository,
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        AccountRepository $accountRepository,
        SettingsCounterRepository $settingsCounterRepository,
        CategoryRepository $categoryRepository,
        EnterpriseHasCouponRepository $enterpriseHasCouponRepository,
        CouponExternalRepository $couponExternalRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->accountRepository = $accountRepository;
        $this->settingsCounterRepository = $settingsCounterRepository;
        $this->categoryRepository = $categoryRepository;
        $this->enterpriseHasCouponRepository = $enterpriseHasCouponRepository;
        $this->couponExternalRepository = $couponExternalRepository;
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
        } else {
            $data['cpf'] = $request->input('cpf') !== null
                ? CpfCnpjRule::normalize($request->input('cpf'))
                : null;
            $data['cnpj'] = $request->input('cnpj') !== null
                ? CpfCnpjRule::normalize($request->input('cnpj'))
                : null;
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

        $cnpj = $request->input('cnpj') !== null
            ? CpfCnpjRule::normalize($request->input('cnpj'))
            : null;
        $cpf = $request->input('cpf') !== null
            ? CpfCnpjRule::normalize($request->input('cpf'))
            : null;

        $enterprise = $this->repository->findById($request->user()->enterprise_id);

        $cnpjEtika = CpfCnpjRule::normalize((string) config('app.cnpj_etika'));

        if ($enterprise->cnpj !== null && CpfCnpjRule::normalize($enterprise->cnpj) === $cnpjEtika) {
            $subscription = $this->subscriptionRepository->findByName('etika');
        } else {
            $subscription = $this->subscriptionRepository->findByName('free');
        }

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
            'cnpj' => $cnpj,
            'cpf' => $cpf,
            'code_financial' => $request->input('code'),
        ];

        $enterprise = $this->repository->createOffice($data);

        $dataAccount = ['name' => 'Caixinha', 'enterprise_id' => $enterprise->id];
        $this->accountRepository->create($dataAccount);
        $this->settingsCounterRepository->create(['enterprise_id' => $enterprise->id, 'allow_add_user' => 1, 'allow_edit_user' => 1, 'allow_delete_user' => 1, 'allow_edit_movement' => 1, 'allow_delete_movement' => 1]);
        CategoryHelper::createDefault($enterprise->id);

        return $enterprise;
    }

    public function update($request)
    {
        $this->rule->update($request);

        $enterprise = $this->repository->findById($request->input('id'));

        $cnpj = $request->input('cnpj') !== null
            ? CpfCnpjRule::normalize($request->input('cnpj'))
            : null;
        $cpf = $request->input('cpf') !== null
            ? CpfCnpjRule::normalize($request->input('cpf'))
            : null;

        if (($cpf && $cpf !== $enterprise->cpf) || ($cnpj && $cnpj !== $enterprise->cnpj)) {
            EnterpriseHelper::existsEnterpriseCpfOrCnpj($request);
        }

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'cnpj' => $cnpj,
            'cpf' => $cpf,
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

    public function getCoupons($enterpriseId)
    {
        return $this->enterpriseHasCouponRepository->getAllByEnterprise($enterpriseId);
    }

    public function setCoupon($enterpriseId, $coupon)
    {
        $coupon = $this->checkCoupon($coupon);

        return $this->enterpriseHasCouponRepository->create([
            'enterprise_id' => $enterpriseId,
            'coupon_id' => $coupon->id,
        ]);
    }

    public function checkCoupon($couponName)
    {
        $coupon = $this->couponExternalRepository->findByName($couponName);
        if (! $coupon) {
            throw new \Exception('O cupom informado não existe', 404);
        }

        if ($coupon->limit) {
            $using = $this->enterpriseHasCouponRepository->countCouponUsing($coupon->id);

            if ($using >= $coupon->limit) {
                throw new \Exception('O cupom informado atingiu o limite de uso', 403);
            }
        }

        return $coupon;
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
