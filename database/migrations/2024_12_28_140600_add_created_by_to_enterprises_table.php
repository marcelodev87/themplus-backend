<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->string('created_by')->nullable()->after('subscription_id');
        });
    }

    public function down()
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
