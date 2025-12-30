<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AsaasWebhook extends Model
{
    use HasUuid, Notifiable;

    protected $table = 'payments_themplus';

    protected $fillable = [
        'event_id',
        'event',
        'change_date',
        'object',
        'payment_id',
        'payment_date_created',
        'customer_id',
        'checkout_session',
        'payment_link',
        'value',
        'net_value',
        'original_value',
        'interest_value',
        'description',
        'billing_type',
        'confirmed_date',
        'pix_transaction',
        'status',
        'due_date',
        'original_due_date',
        'settlement_date',
        'client_payment_date',
        'installment_number',
        'invoice_url',
        'invoice_number',
        'external_reference',
        'deleted',
        'anticipated',
        'anticipable',
        'credit_date',
        'estimated_credit_date',
        'transaction_receipt_url',
        'nosso_numero',
        'bank_slip_url',
        'last_invoice_viewed_date',
        'last_bank_slip_viewed_date',
        'discount_limit_date',
        'due_date_limit_days',
        'discount_value',
        'discount_type',
        'interest_type',
        'fine_value',
        'fine_type',
        'interest_percentage_value',
        'postal_service',
        'escrow_id',
        'escrow_status',
        'escrow_expiration_date',
        'escrow_finish_date',
        'escrow_finish_reason',
        'refund_date_created',
        'refund_status',
        'refund_value',
        'refund_end_to_end_identifier',
        'refund_description',
        'refund_effective_date',
        'refund_transaction_receipt_url',
        'user_id',
        'pix_qr_code_id',
    ];
}
