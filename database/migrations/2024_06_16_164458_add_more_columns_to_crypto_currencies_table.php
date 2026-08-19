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
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('asset_id');
            $table->string('uuid')->unique()->after('id');
            $table->json('changes')->nullable()->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->string('slug')->unique()->after('id');
            $table->dropColumn('uuid');
            $table->dropColumn('changes');
        });
    }
};
