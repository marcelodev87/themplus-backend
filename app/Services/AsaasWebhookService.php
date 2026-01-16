<?php

namespace App\Services;

use App\Enums\Subscription;
use App\Jobs\PaymentMade;
use App\Repositories\AsaasWebhookRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PaymentInfoRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;

class AsaasWebhookService
{
    protected $repository;

    protected $paymentInfoRepository;

    protected $userRepository;

    protected $enterpriseRepository;

    protected $notificationRepository;

    protected $subscriptionRepository;

    public function __construct(
        AsaasWebhookRepository $repository,
        PaymentInfoRepository $paymentInfoRepository,
        UserRepository $userRepository,
        EnterpriseRepository $enterpriseRepository,
        NotificationRepository $notificationRepository,
        SubscriptionRepository $subscriptionRepository,
    ) {
        $this->repository = $repository;
        $this->paymentInfoRepository = $paymentInfoRepository;
        $this->userRepository = $userRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->notificationRepository = $notificationRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function checkWebhook($request)
    {
        $billing = $request['payment']['billingType'];
        $event = $request['event'];

        if ($billing === 'PIX' && $event === 'PAYMENT_RECEIVED') {
            return $this->handlePixPayment($request);
        }

        if ($billing === 'CREDIT_CARD') {
            return $this->handleCreditCard($request);
        }

        return null;
    }

    private function handleCreditCard($request)
    {
        $event = $request['event'];

        if ($event === 'PAYMENT_CREATED') {
            return $this->store($request);
        }

        if ($event === 'PAYMENT_CONFIRMED') {
            return $this->update($request);
        }

        return null;
    }

    private function store($data)
    {
        $parts = explode('|', $data['payment']['externalReference']);
        $userPart = $parts[0];
        $userId = str_replace('user_', '', $userPart);

        $webhook = [
            'event_id' => $data['id'],
            'event' => $data['event'],
            'change_date' => $data['dateCreated'],
            'object' => $data['payment']['object'],
            'payment_id' => $data['payment']['id'],
            'payment_date_created' => $data['payment']['dateCreated'],
            'customer_id' => $data['payment']['customer'],
            'checkout_session' => $data['payment']['checkoutSession'] ?? null,
            'payment_link' => $data['payment']['paymentLink'] ?? null,
            'value' => $data['payment']['value'],
            'net_value' => $data['payment']['netValue'],
            'original_value' => $data['payment']['originalValue'] ?? null,
            'description' => $data['payment']['description'] ?? null,
            'billing_type' => $data['payment']['billingType'],
            'confirmed_date' => $data['payment']['confirmedDate'] ?? null,
            'pix_transaction' => $data['payment']['pixTransaction'] ?? null,
            'status' => $data['payment']['status'],
            'due_date' => $data['payment']['dueDate'],
            'original_due_date' => $data['payment']['originalDueDate'],
            'settlement_date' => $data['payment']['paymentDate'] ?? null,
            'client_payment_date' => $data['payment']['clientPaymentDate'] ?? null,
            'installment_number' => $data['payment']['installmentNumber'] ?? null,
            'invoice_url' => $data['payment']['invoiceUrl'] ?? null,
            'invoice_number' => $data['payment']['invoiceNumber'] ?? null,
            'external_reference' => $data['payment']['externalReference'] ?? null,
            'deleted' => $data['payment']['deleted'],
            'anticipated' => $data['payment']['anticipated'],
            'anticipable' => $data['payment']['anticipable'],
            'credit_date' => $data['payment']['creditDate'] ?? null,
            'estimated_credit_date' => $data['payment']['estimatedCreditDate'] ?? null,
            'transaction_receipt_url' => $data['payment']['transactionReceiptUrl'] ?? null,
            'nosso_numero' => $data['payment']['nossoNumero'] ?? null,
            'bank_slip_url' => $data['payment']['bankSlipUrl'] ?? null,
            'last_invoice_viewed_date' => $data['payment']['lastInvoiceViewedDate'] ?? null,
            'last_bank_slip_viewed_date' => $data['payment']['lastBankSlipViewedDate'] ?? null,
            'discount_value' => $data['payment']['discount']['value'] ?? 0,
            'discount_limit_date' => $data['payment']['discount']['limitDate'] ?? null,
            'due_date_limit_days' => $data['payment']['discount']['dueDateLimitDays'] ?? null,
            'discount_type' => $data['payment']['discount']['type'] ?? null,
            'fine_value' => $data['payment']['fine']['value'] ?? null,
            'fine_type' => $data['payment']['fine']['type'] ?? null,
            'interest_value' => $data['payment']['interest']['value'] ?? null,
            'interest_type' => $data['payment']['interest']['type'] ?? null,
            'postal_service' => $data['payment']['postalService'] ?? null,
            'escrow_id' => $data['payment']['escrow']['id'] ?? null,
            'escrow_status' => $data['payment']['escrow']['status'] ?? null,
            'escrow_expiration_date' => $data['payment']['escrow']['expirationDate'] ?? null,
            'escrow_finish_date' => $data['payment']['escrow']['finishDate'] ?? null,
            'escrow_finish_reason' => $data['payment']['escrow']['finishReason'] ?? null,
            'refund_date_created' => $data['payment']['refunds']['dateCreated'] ?? null,
            'refund_status' => $data['payment']['refunds']['status'] ?? null,
            'refund_value' => $data['payment']['refunds']['value'] ?? null,
            'refund_end_to_end_identifier' => $data['payment']['refunds']['endToEndIdentifier'] ?? null,
            'refund_description' => $data['payment']['refunds']['description'] ?? null,
            'refund_effective_date' => $data['payment']['refunds']['effectiveDate'] ?? null,
            'refund_transaction_receipt_url' => $data['payment']['refunds']['transactionReceiptUrl'] ?? null,
            'user_id' => $userId,
            'pix_qr_code_id' => $data['payment']['pixQrCodeId'] ?? null,
        ];

        return $this->repository->create($webhook);
    }

    private function update($data)
    {
        $parts = explode('|', $data['payment']['externalReference']);
        $userPart = $parts[0];
        $userId = str_replace('user_', '', $userPart);

        $webhook = [
            'event_id' => $data['id'],
            'event' => $data['event'],
            'change_date' => $data['dateCreated'],
            'object' => $data['payment']['object'],
            'payment_id' => $data['payment']['id'],
            'payment_date_created' => $data['payment']['dateCreated'],
            'customer_id' => $data['payment']['customer'],
            'checkout_session' => $data['payment']['checkoutSession'] ?? null,
            'payment_link' => $data['payment']['paymentLink'] ?? null,
            'value' => $data['payment']['value'],
            'net_value' => $data['payment']['netValue'],
            'original_value' => $data['payment']['originalValue'] ?? null,
            'description' => $data['payment']['description'] ?? null,
            'billing_type' => $data['payment']['billingType'],
            'confirmed_date' => $data['payment']['confirmedDate'] ?? null,
            'pix_transaction' => $data['payment']['pixTransaction'] ?? null,
            'status' => $data['payment']['status'],
            'due_date' => $data['payment']['dueDate'],
            'original_due_date' => $data['payment']['originalDueDate'],
            'settlement_date' => $data['payment']['paymentDate'] ?? null,
            'client_payment_date' => $data['payment']['clientPaymentDate'] ?? null,
            'installment_number' => $data['payment']['installmentNumber'] ?? null,
            'invoice_url' => $data['payment']['invoiceUrl'] ?? null,
            'invoice_number' => $data['payment']['invoiceNumber'] ?? null,
            'external_reference' => $data['payment']['externalReference'] ?? null,
            'deleted' => $data['payment']['deleted'],
            'anticipated' => $data['payment']['anticipated'],
            'anticipable' => $data['payment']['anticipable'],
            'credit_date' => $data['payment']['creditDate'] ?? null,
            'estimated_credit_date' => $data['payment']['estimatedCreditDate'] ?? null,
            'transaction_receipt_url' => $data['payment']['transactionReceiptUrl'] ?? null,
            'nosso_numero' => $data['payment']['nossoNumero'] ?? null,
            'bank_slip_url' => $data['payment']['bankSlipUrl'] ?? null,
            'last_invoice_viewed_date' => $data['payment']['lastInvoiceViewedDate'] ?? null,
            'last_bank_slip_viewed_date' => $data['payment']['lastBankSlipViewedDate'] ?? null,
            'discount_value' => $data['payment']['discount']['value'] ?? 0,
            'discount_limit_date' => $data['payment']['discount']['limitDate'] ?? null,
            'due_date_limit_days' => $data['payment']['discount']['dueDateLimitDays'] ?? null,
            'discount_type' => $data['payment']['discount']['type'] ?? null,
            'fine_value' => $data['payment']['fine']['value'] ?? null,
            'fine_type' => $data['payment']['fine']['type'] ?? null,
            'interest_value' => $data['payment']['interest']['value'] ?? null,
            'interest_type' => $data['payment']['interest']['type'] ?? null,
            'postal_service' => $data['payment']['postalService'] ?? null,
            'escrow_id' => $data['payment']['escrow']['id'] ?? null,
            'escrow_status' => $data['payment']['escrow']['status'] ?? null,
            'escrow_expiration_date' => $data['payment']['escrow']['expirationDate'] ?? null,
            'escrow_finish_date' => $data['payment']['escrow']['finishDate'] ?? null,
            'escrow_finish_reason' => $data['payment']['escrow']['finishReason'] ?? null,
            'refund_date_created' => $data['payment']['refunds']['dateCreated'] ?? null,
            'refund_status' => $data['payment']['refunds']['status'] ?? null,
            'refund_value' => $data['payment']['refunds']['value'] ?? null,
            'refund_end_to_end_identifier' => $data['payment']['refunds']['endToEndIdentifier'] ?? null,
            'refund_description' => $data['payment']['refunds']['description'] ?? null,
            'refund_effective_date' => $data['payment']['refunds']['effectiveDate'] ?? null,
            'refund_transaction_receipt_url' => $data['payment']['refunds']['transactionReceiptUrl'] ?? null,
            'user_id' => $userId,
            'pix_qr_code_id' => $data['payment']['pixQrCodeId'] ?? null,
        ];

        $updated = $this->repository->update($webhook['payment_id'], $webhook);

        if ($updated) {
            $this->renewExpirationDate($data);
            PaymentMade::dispatch();
        }

        return $updated;
    }

    private function handlePixPayment($data)
    {
        $pixId = $data['payment']['pixQrCodeId'];

        if (! $this->paymentInfoRepository->findByPaymentID($pixId)) {
            return null;
        }

        $this->store($data);

        $deleted = $this->paymentInfoRepository->deleteByPaymentId($pixId);

        if ($deleted) {
            $this->renewExpirationDate($data);
            \Log::info(['chegou aqui']);
            PaymentMade::dispatch();
        }

        return $deleted;
    }

    private function renewExpirationDate($data)
    {
        [$userPart, $subscriptionPart, $monthQuantityPart] = explode('|', $data['payment']['externalReference']);

        $userID = str_replace('user_', '', $userPart);
        $subscriptionID = str_replace('subscription_', '', $subscriptionPart);
        $monthQuantity = (int) str_replace('month_', '', $monthQuantityPart);

        $user = $this->userRepository->findById($userID);

        $expiredDate = now('America/Sao_Paulo')
            ->addMonths($monthQuantity)
            ->toDateTimeString();

        $this->enterpriseRepository->update($user->enterprise_id, [
            'subscription_id' => $subscriptionID,
            'expired_date' => $expiredDate,
        ]);

        $subscription = $this->subscriptionRepository->findById($subscriptionID);
        $subscriptionName = Subscription::from($subscription->name)->label();

        $expiredDateFormatted = now('America/Sao_Paulo')
            ->addMonths($monthQuantity)->format('d/m/Y H:i:s');

        $this->notificationRepository->create($user->enterprise_id, 'Assinatura renovada!', sprintf(
            "O usuário %s renovou a assinatura com sucesso!\n".
            "Detalhes da Renovação:\n".
            "Plano: %s\n".
            'Novo Vencimento: %s',
            $user->name,
            $subscriptionName,
            $expiredDateFormatted
        ));
    }
}
