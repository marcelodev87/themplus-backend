<?php

namespace App\Services;

use App\Repositories\CounterClientLicenseRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SubscriptionRepository;
use Carbon\Carbon;

class CounterLicenseService
{
    protected $licenseRepository;

    protected $enterpriseRepository;

    protected $subscriptionRepository;

    protected $notificationRepository;

    public function __construct(
        CounterClientLicenseRepository $licenseRepository,
        EnterpriseRepository $enterpriseRepository,
        SubscriptionRepository $subscriptionRepository,
        NotificationRepository $notificationRepository,
    ) {
        $this->licenseRepository = $licenseRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function getUsage($counterEnterpriseId)
    {
        $counter = $this->enterpriseRepository->findById($counterEnterpriseId)->load('subscription');
        $timezone = 'America/Sao_Paulo';
        $now = Carbon::now($timezone);

        $plan = $counter->subscription;
        $planIsCounterPlan = $plan && $plan->type === 'counter';
        $isActive = $planIsCounterPlan
            && $counter->expired_date
            && Carbon::parse($counter->expired_date, $timezone)->greaterThanOrEqualTo($now);

        $limit = $planIsCounterPlan ? $plan->client_limit : 0;
        $used = $this->licenseRepository->countActiveByCounter($counterEnterpriseId);
        $isOverLimit = $limit !== null && $used > $limit;

        return [
            'plan' => $planIsCounterPlan ? $plan : null,
            'expired_date' => $counter->expired_date,
            'is_active' => $isActive,
            'limit' => $limit,
            'used' => $used,
            'is_over_limit' => $isOverLimit,
            'excess' => $isOverLimit ? $used - $limit : 0,
        ];
    }

    public function checkCompliance($counterEnterpriseId)
    {
        $usage = $this->getUsage($counterEnterpriseId);

        if ($usage['is_over_limit']) {
            throw new \Exception(
                "Sua quantidade de clientes com assinatura Básica concedida ({$usage['used']}) está acima do limite do seu plano atual ({$usage['limit']}). ".
                "Desvincule {$usage['excess']} empresa(s) da assinatura Básica antes de continuar."
            );
        }

        return $usage;
    }

    public function grant($counterEnterpriseId, $clientEnterpriseId)
    {
        $client = $this->enterpriseRepository->findById($clientEnterpriseId);

        if (! $client || $client->counter_enterprise_id !== $counterEnterpriseId) {
            throw new \Exception('Esta empresa não está vinculada ao seu escritório');
        }

        if ($this->licenseRepository->findActiveByClient($clientEnterpriseId)) {
            throw new \Exception('Esta empresa já possui uma assinatura Básica concedida');
        }

        $usage = $this->checkCompliance($counterEnterpriseId);

        if (! $usage['is_active']) {
            throw new \Exception('Você não possui um plano de contador ativo para conceder licenças');
        }

        if ($usage['limit'] !== null && $usage['used'] >= $usage['limit']) {
            throw new \Exception('Limite de licenças do seu plano atingido');
        }

        $basic = $this->subscriptionRepository->findByName('basic');

        $this->enterpriseRepository->update($clientEnterpriseId, [
            'subscription_id' => $basic->id,
        ]);

        $license = $this->licenseRepository->create([
            'counter_enterprise_id' => $counterEnterpriseId,
            'client_enterprise_id' => $clientEnterpriseId,
            'subscription_id' => $basic->id,
            'active' => true,
            'granted_at' => now(),
        ]);

        $this->notificationRepository->create(
            $clientEnterpriseId,
            'Assinatura Básica concedida',
            "Sua organização de contabilidade concedeu a você uma assinatura {$basic->name} gratuita."
        );

        $this->notificationRepository->create(
            $counterEnterpriseId,
            'Licença concedida',
            "Você concedeu uma assinatura Básica para {$client->name}. Licenças em uso: {$usage['used']}/".($usage['limit'] ?? 'ilimitado').'.'
        );

        return $license;
    }

    public function revoke($counterEnterpriseId, $clientEnterpriseId)
    {
        $license = $this->licenseRepository->findActiveByClient($clientEnterpriseId);

        if (! $license || $license->counter_enterprise_id !== $counterEnterpriseId) {
            throw new \Exception('Nenhuma licença ativa encontrada para esta empresa');
        }

        return $this->revokeLicense($license);
    }

    public function revokeAllByCounter($counterEnterpriseId)
    {
        $licenses = $this->licenseRepository->getActiveByCounter($counterEnterpriseId);

        foreach ($licenses as $license) {
            $this->revokeLicense($license);
        }

        return $licenses;
    }

    private function revokeLicense($license)
    {
        $client = $this->enterpriseRepository->findById($license->client_enterprise_id);
        $free = $this->subscriptionRepository->findByName('free');

        if ($client && $client->subscription_id === $license->subscription_id) {
            $this->enterpriseRepository->update($client->id, [
                'subscription_id' => $free->id,
            ]);
        }

        $this->licenseRepository->revoke($license->id);

        $this->notificationRepository->create(
            $license->client_enterprise_id,
            'Assinatura Básica removida',
            'Sua assinatura Básica concedida pela organização de contabilidade foi removida. Seu acesso foi alterado para o plano gratuito.'
        );

        return $license;
    }
}
