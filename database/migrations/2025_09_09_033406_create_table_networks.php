<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');

            $table->foreignUuid('member_id')
                ->nullable()
                ->constrained('members');

            $table->foreignUuid('congregation_id')
                ->nullable()
                ->constrained('congregations');

            $table->foreignUuid('enterprise_id')
                ->nullable()
                ->constrained('enterprises');

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
