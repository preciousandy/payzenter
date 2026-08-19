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
        Schema::table('virtual_accounts', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('user_id');
            $table->string('customer_phone')->nullable()->after('customer_email');
            $table->string('provider')->nullable()->after('account_reference');
            $table->string('merchant_ref')->nullable()->after('provider');
            $table->index(['user_id', 'merchant_ref']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('virtual_accounts', function (Blueprint $table) {
            $table->dropColumn(['customer_email', 'customer_phone', 'provider', 'merchant_ref']);
        });
    }
};
