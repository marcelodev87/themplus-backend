<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->dropUnique(['cnpj']);
            $table->dropUnique(['email']);
        });
    }

    public function down()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->unique('cpf');
            $table->unique('cnpj');
            $table->unique('email');
        });
    }
};
