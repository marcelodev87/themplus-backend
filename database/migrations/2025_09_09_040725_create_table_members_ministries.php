<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_ministries', function (Blueprint $table) {
            $table->foreignUuid('member_id')
                ->constrained('members');

            $table->foreignUuid('ministry_id')
                ->constrained('ministries');

            $table->primary(['member_id', 'ministry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_ministries');
    }
};
