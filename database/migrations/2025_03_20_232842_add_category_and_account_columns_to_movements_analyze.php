<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movements_analyze', function (Blueprint $table) {
            $table->foreignUuid('category_id')->constrained('categories')->after('description');
            $table->foreignUuid('account_id')->constrained('accounts')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('movements_analyze', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
