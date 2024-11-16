<?php

namespace App\Services;

use App\Helpers\UserHelper;
use App\Repositories\EnterpriseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Rules\UserRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            throw ValidationException::withMessages([
                'email' => ['Credenciais não constam em nosso registro'],
            ]);
        }
        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Credenciais não constam em nosso registro'],
            ]);
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

    public function include($request)
    {
        $this->rule->include($request);

        $data = $request->only(['name', 'email', 'position', 'phone']);
        $data['password'] = Hash::make($request->input('password'));
        $data['department_id'] = $request->input('department');
        $data['enterprise_id'] = $request->user()->enterprise_id;
        $data['created_by'] = $request->user()->id;

        return $this->repository->create($data);
    }

    public function updateMember($request)
    {
        $this->rule->updateMember($request);

        $data = $request->only(['name', 'email', 'position', 'phone']);
        $data['department_id'] = $request->input('department');

        return $this->repository->updateMember($request->id, $data);
    }

    public function updateData($request)
    {
        $this->rule->updateData($request);

        $data = $request->only(['name', 'email', 'phone']);
        $data['department_id'] = $request->input('department');

        return $this->repository->updateData($request->user()->id, $data);
    }

    public function updatePassword($request)
    {
        $this->rule->updatePassword($request);

        UserHelper::validUser($request->user()->email, $request->input('passwordActual'));

        $data = ['password' => Hash::make($request->input('passwordNew'))];

        return $this->repository->updatePassword($request->user()->id, $data);
    }
}
