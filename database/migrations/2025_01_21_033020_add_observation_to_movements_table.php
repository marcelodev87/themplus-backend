<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->string('observation')->nullable()->after('enterprise_id');
        });
    }

    public function down()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn('observation');
        });
    }
};
