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
        Schema::create('exchange_rate_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domiciliary_bank_id')->references('id')->on('domiciliary_banks')->cascadeOnDelete();
            $table->foreignId('exchange_rate_id')->references('id')->on('exchange_rates')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rate_banks');
    }
};
