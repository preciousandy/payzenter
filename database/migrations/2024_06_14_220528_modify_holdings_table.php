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
        Schema::table('holdings', function (Blueprint $table) {
            $table->dropColumn('dollar_rate');
            $table->dropColumn('asset_price');
            $table->unsignedFloat('asset_amount', 18, 6)->nullable()->after('asset_type');
            $table->unsignedFloat('naira_start_rate', 18)->nullable()->after('amount');
            $table->unsignedFloat('naira_end_rate', 18)->nullable()->after('naira_start_rate');
            $table->time('time')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holdings', function (Blueprint $table) {
            $table->dropColumn('asset_amount');
            $table->dropColumn('naira_start_rate');
            $table->dropColumn('naira_end_rate');
            $table->dropColumn('time');
            $table->unsignedFloat('asset_price')->nullable();
            $table->unsignedFloat('dollar_rate')->nullable();
        });
    }
};
