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
        Schema::table('crypto_addresses', function (Blueprint $table) {
            $table->string('destination_tag')->nullable()->after('network');
        });
        Schema::table('buy_crypto_orders', function (Blueprint $table) {
            $table->string('destination_tag')->nullable()->after('quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crypto_addresses', function (Blueprint $table) {
            $table->dropColumn('destination_tag');
        });

        Schema::table('buy_crypto_orders', function (Blueprint $table) {
            $table->dropColumn('destination_tag');
        });
    }
};
