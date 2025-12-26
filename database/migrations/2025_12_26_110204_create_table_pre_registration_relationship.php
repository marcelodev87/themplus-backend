<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_registration_relationship', function (Blueprint $table) {
            $table->foreignUuid('pre_registration_id')->constrained('pre_registrations');
            $table->string('member');
            $table->string('kinship');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_registration_relationship');
    }
};
