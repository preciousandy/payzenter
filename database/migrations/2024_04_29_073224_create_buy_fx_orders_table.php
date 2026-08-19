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
        Schema::create('buy_fx_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('currency_name');
            $table->string('currency_code');
            $table->unsignedBigInteger('amount');
            $table->unsignedFloat('naira_amount', 18);
            $table->string('acct_name');
            $table->string('acct_number');
            $table->string('acct_bank');
            $table->string('status')->default('pending');
            $table->string('status_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_fx_orders');
    }
};
