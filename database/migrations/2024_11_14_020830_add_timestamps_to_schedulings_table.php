<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedulings', function (Blueprint $table) {
            if (Schema::hasColumn('schedulings', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('schedulings', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        Schema::table('schedulings', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('schedulings', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
