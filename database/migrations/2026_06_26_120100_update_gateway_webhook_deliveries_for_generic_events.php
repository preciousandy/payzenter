<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gateway_webhook_deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('gateway_webhook_deliveries', 'gateway_transaction_id')) {
                $table->unsignedBigInteger('gateway_transaction_id')->nullable()->change();
            }

            if (!Schema::hasColumn('gateway_webhook_deliveries', 'reference')) {
                $table->string('reference', 64)->nullable()->after('merchant_id');
                $table->index(['merchant_id', 'reference']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('gateway_webhook_deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('gateway_webhook_deliveries', 'reference')) {
                $table->dropIndex(['merchant_id', 'reference']);
                $table->dropColumn('reference');
            }

            if (Schema::hasColumn('gateway_webhook_deliveries', 'gateway_transaction_id')) {
                $table->unsignedBigInteger('gateway_transaction_id')->nullable(false)->change();
            }
        });
    }
};
