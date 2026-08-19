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
        if (!Schema::hasTable('swiftpay_transfers')) {
            return;
        }

        if (!Schema::hasColumn('swiftpay_transfers', 'source_link')) {
            Schema::table('swiftpay_transfers', function (Blueprint $table) {
                $table->string('source_link')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('swiftpay_transfers')) {
            return;
        }

        if (Schema::hasColumn('swiftpay_transfers', 'source_link')) {
            Schema::table('swiftpay_transfers', function (Blueprint $table) {
                $table->dropColumn('source_link');
            });
        }
    }
};
