<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('gateway_transactions')
            ->where('status', 'paying')
            ->update(['status' => 'processing']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('gateway_transactions')
            ->where('status', 'processing')
            ->update(['status' => 'paying']);
    }
};

