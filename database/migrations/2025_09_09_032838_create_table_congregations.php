<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('congregations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('date_foundation')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('cep')->nullable();
            $table->string('uf')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('complement')->nullable();
            $table->foreignUuid('member_id')->constrained('members');
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('congregations');
    }
};
