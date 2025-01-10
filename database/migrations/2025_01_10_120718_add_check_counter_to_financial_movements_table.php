<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('financial_movements', function (Blueprint $table) {
            $table->string('check_counter')->nullable()->after('enterprise_id');
        });
    }

    public function down()
    {
        Schema::table('financial_movements', function (Blueprint $table) {
            $table->dropColumn('check_counter');
        });
    }
};
