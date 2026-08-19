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
        if (Schema::hasTable('transfers') && !Schema::hasColumn('transfers', 'source_link')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->string('source_link')->nullable();
            });
        }

        if (Schema::hasTable('swiftpay_transfers') && !Schema::hasColumn('swiftpay_transfers', 'source_link')) {
            Schema::table('swiftpay_transfers', function (Blueprint $table) {
                $table->string('source_link')->nullable();
            });
        }

        if (Schema::hasTable('multiple_transfers') && !Schema::hasColumn('multiple_transfers', 'source_link')) {
            Schema::table('multiple_transfers', function (Blueprint $table) {
                $table->string('source_link')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transfers') && Schema::hasColumn('transfers', 'source_link')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->dropColumn('source_link');
            });
        }

        if (Schema::hasTable('swiftpay_transfers') && Schema::hasColumn('swiftpay_transfers', 'source_link')) {
            Schema::table('swiftpay_transfers', function (Blueprint $table) {
                $table->dropColumn('source_link');
            });
        }

        if (Schema::hasTable('multiple_transfers') && Schema::hasColumn('multiple_transfers', 'source_link')) {
            Schema::table('multiple_transfers', function (Blueprint $table) {
                $table->dropColumn('source_link');
            });
        }
    }
};
