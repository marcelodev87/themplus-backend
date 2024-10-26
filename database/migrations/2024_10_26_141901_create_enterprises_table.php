<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enterprises', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('cnpj')->unique();
            $table->string('cpf')->unique();
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('address');
            $table->string('email')->unique();
            $table->string('phone');
            $table->uuid('subscription_id')->index();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
