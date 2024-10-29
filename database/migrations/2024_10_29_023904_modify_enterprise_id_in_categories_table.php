<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyEnterpriseIdInCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['enterprise_id']);
            $table->dropColumn('enterprise_id');

            $table->foreignUuid('enterprise_id')->nullable()->constrained('enterprises');
            $table->string('type')->nullable();
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['enterprise_id']);
            $table->dropColumn('enterprise_id');

            $table->foreignUuid('enterprise_id')->constrained('enterprises');
            $table->dropColumn('type');
        });
    }
}
