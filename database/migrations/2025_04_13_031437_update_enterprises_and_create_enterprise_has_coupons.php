<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enterprise_has_coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->string('coupon_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprise_has_coupons');
    }
};
