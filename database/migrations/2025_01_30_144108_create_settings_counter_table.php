<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings_counter', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->boolean('allow_add_user')->default(1);
            $table->boolean('allow_edit_user')->default(1);
            $table->boolean('allow_delete_user')->default(1);
            $table->boolean('allow_edit_movement')->default(0);
            $table->boolean('allow_delete_movement')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_counter');
    }
};
