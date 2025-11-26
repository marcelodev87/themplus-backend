<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_relationship', function (Blueprint $table) {
            $table->foreignUuid('member_id')->constrained('members');
            $table->foreignUuid('related_member_id')->constrained('members');
            $table->foreignUuid('relationship_id')->constrained('relationships');
            $table->primary(['member_id', 'related_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_relationship');
    }
};
