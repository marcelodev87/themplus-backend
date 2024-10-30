<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->text('description')->nullable()->after('enterprise_id');
            $table->string('account_number')->nullable()->after('enterprise_id');
            $table->string('agency_number')->nullable()->after('enterprise_id');
        });
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('account_number');
            $table->dropColumn('agency_number');
        });
    }
};
