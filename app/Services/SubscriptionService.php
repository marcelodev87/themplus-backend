<?php

namespace App\Services;

use App\Repositories\EnterpriseRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Rules\SubscriptionRule;

class SubscriptionService
{
    protected $rule;

    protected $userRepository;

    protected $subscriptionRepository;

    protected $enterpriseRepository;

    protected $notificationRepository;

    public function __construct(
        SubscriptionRule $rule,
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        EnterpriseRepository $enterpriseRepository,
        NotificationRepository $notificationRepository,
    ) {
        $this->rule = $rule;
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function updateForFreeSubscription($request)
    {
        $this->rule->create($request);

        $subscription = $this->subscriptionRepository->findById($request->subscriptionID);

        if ($subscription->name === 'free') {
            $user = $this->userRepository->findById($request->user()->id);
            $this->notificationRepository->create($user->enterprise_id, 'Assinatura renovada!', sprintf(
                "O usuário %s renovou a assinatura com sucesso!\n".
                "Detalhes da Renovação:\n".
                'Plano: GRATUITO',
                $user->name,
            ));

            return $this->enterpriseRepository->update($user->enterprise_id, ['subscription_id' => $request->subscriptionID, 'expired_date' => null]);
        }

        return null;
    }
}
