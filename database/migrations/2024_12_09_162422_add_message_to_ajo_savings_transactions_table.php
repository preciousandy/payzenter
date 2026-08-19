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
        Schema::table('ajo_savings_transactions', function (Blueprint $table) {
            $table->string('message')->nullable()->default('Funds deposited')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajo_savings_transactions', function (Blueprint $table) {
            $table->dropColumn('message');
        });
    }
};
