<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gateway_transactions')) {
            return;
        }

        Schema::table('gateway_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('gateway_transactions', 'provider')) {
                $table->string('provider', 20)->default('palmpay')->after('status');
            }
            if (!Schema::hasColumn('gateway_transactions', 'provider_reference')) {
                $table->string('provider_reference', 64)->nullable()->after('provider');
            }
            if (!Schema::hasColumn('gateway_transactions', 'provider_transaction_id')) {
                $table->string('provider_transaction_id', 64)->nullable()->after('provider_reference');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('gateway_transactions')) {
            return;
        }

        Schema::table('gateway_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('gateway_transactions', 'provider_transaction_id')) {
                $table->dropColumn('provider_transaction_id');
            }
            if (Schema::hasColumn('gateway_transactions', 'provider_reference')) {
                $table->dropColumn('provider_reference');
            }
            if (Schema::hasColumn('gateway_transactions', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }
};

