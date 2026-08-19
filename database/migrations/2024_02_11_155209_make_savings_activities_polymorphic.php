<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('savings_activities', function (Blueprint $table) {
            $table->dropForeign(['savings_id']);
            $table->dropColumn('savings_id');
            $table->morphs('modelable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_activities', function (Blueprint $table) {
            $table->dropMorphs('modelable');
            $table->foreignId('savings_id')->constrained();
        });
    }
};
