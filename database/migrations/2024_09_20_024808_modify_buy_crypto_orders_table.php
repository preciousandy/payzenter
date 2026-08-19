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
            $table->string('crypto_network')->nullable()->after('wallet_address');
            $table->string('quotation_id')->nullable()->after('crypto_network');

            $table->dropColumn('status_message');
            $table->dropColumn('amount_in_usd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buy_crypto_orders', function (Blueprint $table) {
            $table->dropColumn('crypto_network');
            $table->dropColumn('quotation_id');

            $table->string('status_message')->nullable();
            $table->decimal('amount_in_usd', 15, 2)->nullable();
        });
    }
};
