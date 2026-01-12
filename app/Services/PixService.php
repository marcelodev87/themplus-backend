<?php

namespace App\Services;

use App\Http\AsaasHttpClient;
use App\Repositories\PaymentInfoRepository;
use App\Repositories\SubscriptionRepository;
use App\Rules\SubscriptionRule;

class PixService
{
    protected $http;

    protected $subscriptionRepository;

    protected $paymentInfoRepository;

    protected $addressKey;

    protected $rule;

    public function __construct(SubscriptionRepository $subscriptionRepository, AsaasHttpClient $http, PaymentInfoRepository $paymentInfoRepository, SubscriptionRule $rule)
    {
        $this->http = $http;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->paymentInfoRepository = $paymentInfoRepository;
        $this->addressKey = config('app.asaas_address_key');
        $this->rule = $rule;
    }

    public function payment($request)
    {
        $this->rule->create($request);
        $subscription = $this->subscriptionRepository->findById($request->subscriptionID);

        $data = [
            'value' => $subscription->price,
            'userID' => $request->user()->id,
            'subscriptionID' => $subscription->id,
            'monthQuantity' => 1,
        ];

        return $this->create($data);
    }

    public function create($data)
    {
        $externalReference = sprintf(
            'user_%s|subscription_%s|month_%s',
            $data['userID'],
            $data['subscriptionID'],
            $data['monthQuantity']
        );

        $pixData = [
            'format' => 'ALL',
            'addressKey' => $this->addressKey,
            'value' => $data['value'],
            'externalReference' => $externalReference,
        ];

        $response = $this->http->post('/pix/qrCodes/static', $pixData);

        $paymentData = $response;

        if ($this->existsPixTransaction($data['userID'])) {
            $this->updateTransaction($paymentData);
        } else {
            $this->saveTransaction($paymentData);
        }

        return $response;
    }

    private function existsPixTransaction(string $userID): bool
    {
        return ! is_null($this->paymentInfoRepository->findByUserId($userID));
    }

    private function updateTransaction(array $paymentData)
    {
        $parts = explode('|', $paymentData['externalReference']);
        $userPart = $parts[0];
        $subscriptionPart = $parts[1];
        $monthPart = $parts[2];
        $userId = str_replace('user_', '', $userPart);
        $subscriptionId = str_replace('subscription_', '', $subscriptionPart);
        $monthQuantity = str_replace('month_', '', $monthPart);

        $paymentInfoData = [
            'payment_id' => $paymentData['id'],
            'user_id' => $userId,
            'subscription_id' => $subscriptionId,
            'month_quantity' => $monthQuantity,
        ];

        $userID = $paymentInfoData['user_id'];

        return $this->paymentInfoRepository->update($userID, $paymentInfoData);
    }

    private function saveTransaction(array $paymentData)
    {
        $parts = explode('|', $paymentData['externalReference']);
        $userPart = $parts[0];
        $subscriptionPart = $parts[1];
        $monthPart = $parts[2];
        $userId = str_replace('user_', '', $userPart);
        $subscriptionId = str_replace('subscription_', '', $subscriptionPart);
        $monthQuantity = str_replace('month_', '', $monthPart);

        $paymentInfoData = [
            'payment_id' => $paymentData['id'],
            'user_id' => $userId,
            'subscription_id' => $subscriptionId,
            'month_quantity' => $monthQuantity,
        ];

        return $this->paymentInfoRepository->create($paymentInfoData);
    }
}
