<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->text('observation')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->string('observation', 255)->nullable()->change();
        });
    }
};
