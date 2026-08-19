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
            $table->float('change_percentage')->nullable()->after('price_in_usd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->dropColumn('change_percentage');
        });
    }
};
