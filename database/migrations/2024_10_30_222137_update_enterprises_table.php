<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnterprisesTable extends Migration
{
    public function up()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->string('cep')->after('cpf')->nullable();
            $table->string('neighborhood')->after('city')->nullable();
            $table->string('number_address')->after('address')->nullable();
            $table->string('complement')->after('address')->nullable();
        });
    }

    public function down()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->string('country');
            $table->dropColumn('cep');
            $table->dropColumn('neighborhood');
            $table->dropColumn('number_address');
            $table->dropColumn('complement');
        });
    }
}
