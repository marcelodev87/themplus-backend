<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->string('cnpj')->nullable()->change();
            $table->string('cpf')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->string('cnpj')->nullable(false)->change();
            $table->string('cpf')->nullable(false)->change();
        });
    }
};
