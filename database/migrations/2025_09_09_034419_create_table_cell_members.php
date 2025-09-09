<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cell_members', function (Blueprint $table) {
            $table->foreignUuid('member_id')
                ->constrained('members');

            $table->foreignUuid('cell_id')
                ->constrained('members');

            $table->primary(['member_id', 'cell_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cell_members');
    }
};
