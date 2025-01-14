<?php

namespace App\Services;

use App\Helpers\UserHelper;
use App\Jobs\SendResetPasswordEmail;
use App\Models\PasswordReset;
use App\Repositories\AccountRepository;
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

    protected $accountRepository;

    public function __construct(
        UserRule $rule,
        UserRepository $repository,
        EnterpriseRepository $enterpriseRepository,
        SubscriptionRepository $subscriptionRepository,
        AccountRepository $accountRepository,
    ) {
        $this->rule = $rule;
        $this->repository = $repository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->accountRepository = $accountRepository;
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

        $this->repository->update($user->id, ['view_enterprise_id' => $user->enterprise_id]);

        $user = $this->repository->findByEmail($data['email']);

        return $user;
    }

    public function reset($request)
    {
        $this->rule->reset($request);
        $user = $this->repository->findByEmail($request->input('email'));

        if ($user) {
            $token = app('auth.password.broker')->createToken($user);
            SendResetPasswordEmail::dispatch($user, $token);
        } else {
            throw ValidationException::withMessages([
                'email' => ['O e-mail não está cadastrado.'],
            ]);
        }

        return 'O e-mail informado receberá um código para redefinir sua senha';
    }

    public function verify($request)
    {
        $this->rule->verify($request);
        $reset = PasswordReset::where('email', $request->input('email'))->first();

        if ($reset && $reset->code === $request->input('code')) {
            return ['valid' => true, 'message' => 'Código verificado com sucesso'];
        }

        return ['valid' => false, 'message' => 'Código incorreto ou expirado'];
    }

    public function resetPassword($request)
    {
        $this->rule->resetPassword($request);

        $data = ['password' => Hash::make($request->input('password'))];

        $result = $this->repository->resetPassword($request->input('email'), $data);

        $register = PasswordReset::where('email', $request->input('email'))->first();
        if ($register) {
            $register->delete();
        }

        return $result;
    }

    public function create($request)
    {
        $this->rule->create($request);

        $data = $request->only(['name', 'password', 'email', 'nameEnterprise', 'position']);
        $data['password'] = Hash::make($data['password']);

        $subscription = $this->subscriptionRepository->findByName('free');
        $dataEnterprise = [
            'name' => $data['nameEnterprise'],
            'subscription_id' => $subscription->id,
            'position' => $data['position'],
        ];
        $enterprise = $this->enterpriseRepository->createStart($dataEnterprise);

        $dataAccount = ['name' => 'Caixinha', 'enterprise_id' => $enterprise->id];
        $this->accountRepository->create($dataAccount);

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
        $data['view_enterprise_id'] = $request->user()->enterprise_id;
        $data['created_by'] = $request->user()->id;

        return $this->repository->create($data);
    }

    public function startOfficeNewUser($request)
    {
        $this->rule->startOfficeNewUser($request);

        $data = $request->only(['name', 'email', 'position', 'phone']);
        $data['password'] = Hash::make($request->input('password'));
        $data['department_id'] = $request->input('department');
        $data['enterprise_id'] = $request->input('enterpriseId');

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
