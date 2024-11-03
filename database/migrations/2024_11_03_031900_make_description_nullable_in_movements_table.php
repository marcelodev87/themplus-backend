<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->string('description')->nullable(false)->change();
        });
    }
};
