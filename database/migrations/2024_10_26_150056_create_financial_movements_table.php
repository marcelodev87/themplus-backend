<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('date_delivery');
            $table->integer('month');
            $table->integer('year');
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_movements');
    }
};
