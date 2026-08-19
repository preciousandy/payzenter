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
        Schema::table('sell_crypto_orders', function (Blueprint $table) {
            $table->dropColumn('amount_in_usd');
            $table->dropColumn('status_message');
            $table->dropColumn('transaction_id');
            $table->dropColumn('screenshot');
            $table->dropColumn('wallet_address');

            $table->decimal('amount_in_crypto', 15, 10)->change();
            $table->string('quotation_id')->nullable();
            $table->string('crypto_network')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
