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
        Schema::create('ajo_savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajo_savings_id')->constrained();
            $table->unsignedBigInteger('amount');
            $table->string('source');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajo_savings_transactions');
    }
};
