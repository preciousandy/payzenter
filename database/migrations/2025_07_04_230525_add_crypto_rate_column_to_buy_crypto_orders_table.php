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

        Schema::table('buy_crypto_orders', function (Blueprint $table) {
            $table->unsignedFloat('crypto_rate', 8, 2)
                ->nullable()
                ->after('crypto_fee');
        });

        Schema::table('sell_crypto_orders', function (Blueprint $table) {

            $table->unsignedFloat('crypto_rate', 8, 2)
                ->nullable()
                ->after('amount_in_crypto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buy_crypto_orders', function (Blueprint $table) {
            $table->dropColumn('crypto_rate');
        });

        Schema::table('sell_crypto_orders', function (Blueprint $table) {
            $table->dropColumn('crypto_rate');
        });
    }
};
