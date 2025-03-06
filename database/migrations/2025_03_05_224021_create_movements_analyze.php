<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('movements_analyze', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date_movement');
            $table->string('type');
            $table->decimal('value', 13, 2)->default(0);
            $table->text('receipt')->nullable();
            $table->text('description')->nullable();
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements_analyze');
    }
};
