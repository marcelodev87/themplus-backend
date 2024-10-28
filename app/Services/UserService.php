<?php

namespace App\Services;

use App\Repositories\EnterpriseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Rules\UserRule;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $rule;

    protected $repository;

    protected $enterpriseRepository;

    protected $subscriptionRepository;

    public function __construct(
        UserRule $rule,
        UserRepository $repository,
        EnterpriseRepository $enterpriseRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function login($request)
    {
        $this->rule->login($request);

        $data = $request->only(['password', 'email']);

        $user = $this->repository->findByEmail($data['email']);
        if (! $user) {
            throw new ValidationException('Este e-mail não está registrado');
        }
        if (! Hash::check($data['password'], $user->password)) {
            throw new ValidationException('A senha está incorreta');
        }

        return $user;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name', 'password', 'email', 'nameEnterprise']);
        $data['password'] = Hash::make($data['password']);

        $subscription = $this->subscriptionRepository->findByName('free');
        $enterprise = $this->enterpriseRepository->createStart($data['nameEnterprise'], $subscription->id);

        $data['enterprise_id'] = $enterprise->id;
        $data['position'] = 'admin';
        unset($data['nameEnterprise']);

        return $this->repository->create($data);
    }
}
