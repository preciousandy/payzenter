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
        Schema::table('buy_fx_orders', function (Blueprint $table) {
            $table->json('data')->nullable();

            $table->dropColumn('acct_name');
            $table->dropColumn('acct_number');
            $table->dropColumn('acct_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buy_fx_orders', function (Blueprint $table) {
            $table->dropColumn('data');

            $table->string('acct_name')->nullable();
            $table->string('acct_number')->nullable();
            $table->string('acct_bank')->nullable();
        });
    }
};
