<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_movements_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('receipt');
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->foreignUuid('financial_movements_id')->constrained('financial_movements');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_movements_receipts');
    }
};
