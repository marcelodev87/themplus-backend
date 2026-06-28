<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_movements', function (Blueprint $table) {
            $table->string('check_counter_user')->nullable();
            $table->timestamp('check_counter_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('financial_movements', function (Blueprint $table) {
            $table->dropColumn(['check_counter_user', 'check_counter_date']);
        });
    }
};
