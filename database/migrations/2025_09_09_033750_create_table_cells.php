<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cells', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('date_foundation')->nullable();
            $table->string('date_end')->nullable();
            $table->foreignUuid('network_id')->nullable()->constrained('networks');
            $table->foreignUuid('leader_id')->nullable()->constrained('members');
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->foreignUuid('host_id')->nullable()->constrained('members');
            $table->boolean('active')->default(1);
            $table->string('location');
            $table->integer('day_week');
            $table->string('frequency');
            $table->string('time');
            $table->string('cep')->nullable();
            $table->string('uf')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('complement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cells');
    }
};
