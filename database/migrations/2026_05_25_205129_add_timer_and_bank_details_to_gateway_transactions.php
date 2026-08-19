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
        Schema::table('gateway_transactions', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable();
            $table->string('bank_transfer_account', 20)->nullable();
            $table->string('bank_transfer_bank', 100)->nullable();
            $table->string('bank_transfer_name', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateway_transactions', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'bank_transfer_account', 'bank_transfer_bank', 'bank_transfer_name']);
        });
    }
};
