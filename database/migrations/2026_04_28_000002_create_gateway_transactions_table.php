<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateway_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference', 64)->unique();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 10)->default('NGN');
            $table->string('method', 20);
            $table->string('status', 20)->default('unpaid');

            $table->string('palmpay_order_id', 32)->nullable()->index();
            $table->string('palmpay_order_no', 64)->nullable()->index();
            $table->text('checkout_url')->nullable();

            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_name')->nullable();

            $table->text('return_url')->nullable();
            $table->text('merchant_webhook_url')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->json('raw_provider_response')->nullable();
            $table->json('raw_provider_webhook')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateway_transactions');
    }
};

