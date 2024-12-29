<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->string('position')->default('client')->after('name');
            $table->uuid('counter_enterprise_id')->nullable()->after('position');
            $table->foreign('counter_enterprise_id')->references('id')->on('enterprises')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropForeign(['counter_enterprise_id']);
            $table->dropColumn('counter_enterprise_id');
            $table->dropColumn('position');
        });
    }
};
