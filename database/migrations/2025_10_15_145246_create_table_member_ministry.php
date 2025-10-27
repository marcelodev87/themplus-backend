<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('member_ministry', function (Blueprint $table) {
            $table->foreignUuid('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignUuid('ministry_id')->constrained('ministries')->onDelete('cascade');
            $table->primary(['member_id', 'ministry_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_ministry');
    }
};
