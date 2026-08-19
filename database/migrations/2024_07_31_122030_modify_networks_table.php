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
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->string('airtime_biller_code')->nullable();
            $table->string('data_biller_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->dropColumn('airtime_biller_code');
            $table->dropColumn('data_biller_code');
        });
    }
};
