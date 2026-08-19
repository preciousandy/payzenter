<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gateway_webhook_deliveries')) {
            return;
        }

        Schema::create('gateway_webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gateway_transaction_id')->constrained('gateway_transactions')->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained('users')->cascadeOnDelete();
            $table->string('url', 500);
            $table->string('event', 100)->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->longText('response_body')->nullable();
            $table->string('error_message', 500)->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['merchant_id', 'created_at']);
            $table->index(['merchant_id', 'delivered_at']);
            $table->index(['merchant_id', 'last_attempt_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateway_webhook_deliveries');
    }
};

