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
        Schema::table('transfer_rates', function (Blueprint $table) {
            $table->unsignedFloat('fixed_fee')->after('rate')->nullable();
            $table->unsignedFloat('percentage_fee')->after('fixed_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_rates', function (Blueprint $table) {
            $table->dropColumn('fixed_fee');
            $table->dropColumn('percentage_fee');
        });
    }
};
