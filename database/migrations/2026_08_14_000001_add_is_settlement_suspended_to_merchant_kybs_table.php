<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_kybs', function (Blueprint $table) {
            $table->boolean('is_settlement_suspended')->default(false)->after('settlement_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_kybs', function (Blueprint $table) {
            $table->dropColumn('is_settlement_suspended');
        });
    }
};
