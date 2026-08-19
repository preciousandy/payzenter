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
        Schema::create('sell_crypto_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('crypto_name');
            $table->string('crypto_symbol');
            $table->unsignedBigInteger('amount_in_usd');
            $table->unsignedBigInteger('amount_in_naira');
            $table->decimal('amount_in_crypto', 15, 15);
            $table->string('status')->default('pending');
            $table->text('status_message')->nullable();
            $table->string('wallet_address')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('screenshot')->nullable();
            $table->timestamps();
        });
    }

    /**unsignedBigInteger
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_crypto_orders');
    }
};
