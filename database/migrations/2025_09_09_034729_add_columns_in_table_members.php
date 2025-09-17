<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->boolean('active')->nullable();
            $table->string('date_baptismo')->nullable();
            $table->string('start_date')->nullable();
            $table->string('reason_start_date')->nullable();
            $table->string('church_start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('reason_end_date')->nullable();
            $table->string('church_end_date')->nullable();
            $table->foreignUuid('role_id')->nullable()->constrained('roles');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['congregation_id']);
            $table->dropForeign(['role_id']);

            $table->dropColumn([
                'type',
                'active',
                'date_baptismo',
                'start_date',
                'reason_start_date',
                'church_start_date',
                'end_date',
                'reason_end_date',
                'church_end_date',
                'role_id',
            ]);
        });
    }
};
