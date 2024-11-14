<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('schedulings', function (Blueprint $table) {
            $table->string('type')->nullable(false);
            $table->integer('status')->default(0)->nullable(false);
            $table->decimal('value', 10, 2)->nullable(false);
            $table->string('description')->nullable();
            $table->string('receipt')->nullable();
            $table->foreignUuid('category_id')->constrained('categories');
            $table->foreignUuid('account_id')->constrained('accounts');
        });
    }

    public function down()
    {
        Schema::table('schedulings', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
            $table->dropColumn('category_id');
            $table->dropColumn('type');
            $table->dropColumn('status');
            $table->dropColumn('value');
            $table->dropColumn('description');
            $table->dropColumn('receipt');
        });
    }
};
