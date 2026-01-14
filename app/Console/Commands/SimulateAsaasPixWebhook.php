<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SimulateAsaasPixWebhook extends Command
{
    protected $signature = 'simulate:asaas-pix-webhook {pixQrCodeId}';

    protected $description = 'Simula um POST de webhook Asaas com dados de PIX';

    public function handle()
    {
        $pixQrCodeId = $this->argument('pixQrCodeId');

        $externalReference =
                'user_' . env('USER_ID_SIMULATE_PAYMENT_ASAAS') .
                '|subscription_' . env('SUBSCRIPTION_ID_SIMULATE_PAYMENT_ASAAS') .
                '|month_1';

        $payload = [
            'id' => 'evt_05b708f961d739ea7eba7e4db318f621&368604920',
            'event' => 'PAYMENT_RECEIVED',
            'dateCreated' => '2024-06-12 16:45:03',
            'payment' => [
                'object' => 'payment',
                'id' => 'pay_080225913252',
                'dateCreated' => '2021-01-01',
                'customer' => 'cus_G7Dvo4iphUNk',
                'subscription' => null,
                'installment' => null,
                'paymentLink' => null,
                'dueDate' => Carbon::now('America/Sao_Paulo')->format('Y-m-d'),
                'originalDueDate' => Carbon::now('America/Sao_Paulo')->format('Y-m-d'),
                'value' => 129,
                'netValue' => 94.51,
                'originalValue' => null,
                'interestValue' => null,
                'nossoNumero' => null,
                'description' => 'Pedido 056984',
                'externalReference' => $externalReference,
                'billingType' => 'PIX',
                'pixQrCodeId' => $pixQrCodeId,
                'status' => 'RECEIVED',
                'pixTransaction' => null,
                'confirmedDate' => '2021-01-01',
                'paymentDate' => '2021-01-01',
                'clientPaymentDate' => '2021-01-01',
                'installmentNumber' => null,
                'creditDate' => '2021-02-01',
                'custody' => null,
                'estimatedCreditDate' => '2021-02-01',
                'invoiceUrl' => 'https://www.asaas.com/i/080225913252',
                'bankSlipUrl' => null,
                'transactionReceiptUrl' => null,
                'invoiceNumber' => '00005101',
                'deleted' => false,
                'anticipated' => false,
                'anticipable' => false,
                'lastInvoiceViewedDate' => '2021-01-01 12:54:56',
                'lastBankSlipViewedDate' => null,
                'postalService' => false,
                'creditCard' => null,
                'discount' => [
                    'value' => 0.00,
                    'dueDateLimitDays' => 0,
                    'limitedDate' => null,
                    'type' => 'FIXED',
                ],
                'fine' => [
                    'value' => 0.00,
                    'type' => 'FIXED',
                ],
                'interest' => [
                    'value' => 0.00,
                    'type' => 'PERCENTAGE',
                ],
                'split' => null,
                'chargeback' => null,
                'refunds' => null,
            ],
        ];

        if (app()->environment('local')) {
            $ngrokDomain = env('NGROK_DOMAIN');
            $url = "https://{$ngrokDomain}/api/webhook-asaas/";
        } else {
            $url = rtrim(config('app.url'), '/') . '/api/webhook-asaas/';
        }

        $response = Http::withHeaders([
            'asaas-access-token' => env('ASAAS_WEBHOOK_TOKEN'),
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        $this->info("POST enviado para $url. Status HTTP: {$response->status()}");
        $this->info('Resposta: '.$response->body());
    }
}
