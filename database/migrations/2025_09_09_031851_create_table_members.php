<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('profession')->nullable();
            $table->string('date_birth')->nullable();
            $table->string('naturalness')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('education')->nullable();
            $table->string('cpf')->nullable();
            $table->string('email')->nullable();
            $table->string('email_professional')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_professional')->nullable();
            $table->string('cep')->nullable();
            $table->string('uf')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('complement')->nullable();
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
