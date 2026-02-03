<?php

namespace App\Services;

use App\Http\AsaasHttpClient;
use App\Repositories\SubscriptionRepository;
use App\Rules\CreditCardRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreditCardService
{
    protected $rule;

    protected $subscriptionRepository;

    protected $http;

    public function __construct(
        CreditCardRule $rule,
        SubscriptionRepository $subscriptionRepository,
        AsaasHttpClient $http,
    ) {
        $this->rule = $rule;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->http = $http;
    }

    public function payment($request)
    {
        $this->rule->create($request);
        $subscription = $this->subscriptionRepository->findById($request->subscriptionID);

        $available = explode(',', env('SUBSCRIPTIONS_AVAILABLE', ''));

        if (! in_array($subscription->name, $available)) {
            throw new \Exception('Assinatura não permitida');
        }

        $data = [
            'userID' => Auth::user()->id,
            'subscriptionID' => $subscription->id,
            'monthQuantity' => 1,
            'value' => $subscription->price,
            'description' => null,
            'installmentCount' => null,
            'creditCard' => $request->creditCard,
            'creditCardHolderInfo' => $request->creditCardHolderInfo,
        ];

        $customers = $this->http->get('/customers');

        $customerID = $this->existsClient($customers['data'], $request->creditCardHolderInfo['cpfCnpj']) ?: $this->createNewClient($request)['id'];

        $charge = $this->createCreditCardCharge($customerID, $data);

        $payment = $this->payCreditCardCharge($charge['id'], $request);

        return $this->isItPaid($payment['id']);
    }

    private function createCreditCardCharge(string $customerID, $data)
    {
        $userID = $data['userID'];
        $subscriptionID = $data['subscriptionID'];
        $monthQuantity = $data['monthQuantity'];
        $chargeData = [
            'customer' => $customerID,
            'billingType' => 'CREDIT_CARD',
            'value' => $data['value'],
            'dueDate' => Carbon::now()->addHours(24)->toDateString(),
            'externalReference' => "user_{$userID}|subscription_{$subscriptionID}|month_{$monthQuantity}",
            'description' => $data['description'] ?? null,
            'installmentCount' => $data['installmentCount'] ?? null,
            'totalValue' => $data['totalValue'] ?? null,
            'installmentValue' => $data['installmentValue'] ?? null,
        ];

        $response = $this->http->post('/payments', $chargeData);

        return $response;
    }

    private function payCreditCardCharge(string $chargeID, $data)
    {
        $paymentData = [
            'creditCard' => [
                'holderName' => $data['creditCard']['holderName'],
                'number' => $data['creditCard']['number'],
                'expiryMonth' => $data['creditCard']['expiryMonth'],
                'expiryYear' => $data['creditCard']['expiryYear'],
                'ccv' => $data['creditCard']['ccv'],
            ],
            'creditCardHolderInfo' => [
                'name' => $data['creditCardHolderInfo']['name'],
                'email' => $data['creditCardHolderInfo']['email'],
                'cpfCnpj' => $data['creditCardHolderInfo']['cpfCnpj'],
                'postalCode' => $data['creditCardHolderInfo']['postalCode'],
                'addressNumber' => $data['creditCardHolderInfo']['addressNumber'],
                'addressComplement' => $data['creditCardHolderInfo']['addressComplement'] ?? null,
                'phone' => $data['creditCardHolderInfo']['phone'],
            ],
        ];

        $response = $this->http->post(
            "/payments/{$chargeID}/payWithCreditCard",
            $paymentData
        );

        return $response;
    }

    private function existsClient(array $clients, string $cpfCnpj): ?string
    {
        foreach ($clients as $customer) {
            if (($customer['cpfCnpj'] ?? null) === $cpfCnpj) {
                return $customer['id'];
            }
        }

        return null;
    }

    private function createNewClient($request)
    {
        $info = $request['creditCardHolderInfo'];

        return $this->http->post('/customers', [
            'name' => $info['name'],
            'cpfCnpj' => $info['cpfCnpj'],
            'email' => $info['email'],
            'mobilePhone' => $info['phone'],
        ]);
    }

    private function isItPaid(string $paymentID): bool
    {
        $response = $this->http->get("/payments/{$paymentID}");

        return $response['status'] === 'CONFIRMED';
    }
}
