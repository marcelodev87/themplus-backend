<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('member_id')->nullable()->constrained('members');
            $table->foreignUuid('enterprise_id')->nullable()->constrained('enterprises');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministries');
    }
};
