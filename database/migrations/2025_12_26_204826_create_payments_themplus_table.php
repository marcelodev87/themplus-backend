<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments_themplus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_id');
            $table->string('event');
            $table->string('change_date');
            $table->string('object');
            $table->string('payment_id');
            $table->date('payment_date_created');
            $table->string('customer_id');
            $table->string('checkout_session')->nullable();
            $table->string('payment_link')->nullable();
            $table->decimal('value', 10, 2);
            $table->decimal('net_value', 10, 2);
            $table->decimal('original_value', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('billing_type');
            $table->string('confirmed_date')->nullable();
            $table->string('pix_transaction')->nullable();
            $table->string('status');
            $table->string('due_date');
            $table->string('original_due_date');
            $table->string('settlement_date')->nullable();
            $table->string('client_payment_date')->nullable();
            $table->integer('installment_number')->nullable();
            $table->string('invoice_url');
            $table->integer('invoice_number');
            $table->string('external_reference')->nullable();
            $table->boolean('deleted');
            $table->boolean('anticipated');
            $table->boolean('anticipable');
            $table->string('credit_date')->nullable();
            $table->string('estimated_credit_date')->nullable();
            $table->string('transaction_receipt_url')->nullable();
            $table->string('nosso_numero')->nullable();
            $table->string('bank_slip_url')->nullable();
            $table->string('last_invoice_viewed_date')->nullable();
            $table->string('last_bank_slip_viewed_date')->nullable();
            $table->string('discount_limit_date')->nullable();
            $table->string('due_date_limit_days');
            $table->decimal('discount_value', 10, 2);
            $table->string('discount_type');
            $table->decimal('fine_value', 10, 2);
            $table->string('fine_type');
            $table->decimal('interest_value', 10, 2)->nullable();
            $table->string('interest_type');
            $table->boolean('postal_service');
            $table->string('escrow_id')->nullable();
            $table->string('escrow_status')->nullable();
            $table->string('escrow_expiration_date')->nullable();
            $table->string('escrow_finish_date')->nullable();
            $table->text('escrow_finish_reason')->nullable();
            $table->string('refund_date_created')->nullable();
            $table->string('refund_status')->nullable();
            $table->decimal('refund_value', 10, 2)->nullable();
            $table->string('refund_end_to_end_identifier')->nullable();
            $table->text('refund_description')->nullable();
            $table->string('refund_effective_date')->nullable();
            $table->string('refund_transaction_receipt_url')->nullable();
            $table->string('user_id')->nullable();
            $table->string('pix_qr_code_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_themplus');
    }
};
