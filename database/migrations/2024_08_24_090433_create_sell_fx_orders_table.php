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
        Schema::create('sell_fx_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->foreignId('user_id')->onDelete('cascade');
            $table->string('currency_name');
            $table->string('currency_code');
            $table->unsignedBigInteger('amount');
            $table->unsignedFloat('naira_amount', 18);
            $table->string('status')->default('pending');
            $table->string('status_message')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::table('foreign_exchange_settings', function (Blueprint $table) {
            $table->json('sell_fx_form_fields')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_fx_orders');

        Schema::table('foreign_exchange_settings', function (Blueprint $table) {
            $table->dropColumn('sell_fx_form_fields');
        });
    }
};
