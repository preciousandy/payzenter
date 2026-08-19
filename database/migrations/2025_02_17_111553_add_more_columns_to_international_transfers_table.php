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
        Schema::table('international_transfers', function (Blueprint $table) {
            $table->string('postal_code')->nullable();
            $table->string('street_name')->nullable();
            $table->string('street_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('international_transfers', function (Blueprint $table) {
            $table->dropColumn([
                'postal_code',
                'street_name',
                'street_number',
                'city'
            ]);
        });
    }
};
