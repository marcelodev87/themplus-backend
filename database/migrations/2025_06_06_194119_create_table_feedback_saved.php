<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('feedbacks_saved', function(Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_name');
            $table->string('user_email');
            $table->string('enterprise_name');
            $table->longText('message');
            $table->string('date_feedback');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('feedbacks_saved');
    }
};

