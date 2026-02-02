<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments_themplus', function (Blueprint $table) {
            $table->decimal('fine_value', 10, 2)->nullable()->change();
            $table->string('fine_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments_themplus', function (Blueprint $table) {
            $table->dropColumn('fine_value')->nullable(false)->change();
            $table->dropColumn('fine_type')->nullable(false)->change();
        });
    }
};
