<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counter_client_licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('counter_enterprise_id');
            $table->uuid('client_enterprise_id');
            $table->uuid('subscription_id');
            $table->boolean('active')->default(true);
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->foreign('counter_enterprise_id')->references('id')->on('enterprises')->cascadeOnDelete();
            $table->foreign('client_enterprise_id')->references('id')->on('enterprises')->cascadeOnDelete();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');

            $table->index(['counter_enterprise_id', 'active']);
            $table->index(['client_enterprise_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counter_client_licenses');
    }
};
