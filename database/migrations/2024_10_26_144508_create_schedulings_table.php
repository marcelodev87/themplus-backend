<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedulings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date_movement');
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedulings');
    }
};
