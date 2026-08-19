<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gateway_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('charged_amount')->nullable();
            $table->string('charge_mode')->default('merchant');
        });
    }

    public function down(): void
    {
        Schema::table('gateway_transactions', function (Blueprint $table) {
            $table->dropColumn('charged_amount');
            $table->dropColumn('charge_mode');
        });
    }
};
