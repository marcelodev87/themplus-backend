<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['alert_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('alert_id');
        });

        Schema::dropIfExists('alerts');
    }

    public function down()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description');
            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignUuid('alert_id')->nullable()->constrained('alerts');
        });
    }
};
