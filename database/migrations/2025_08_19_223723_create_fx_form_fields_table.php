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
        Schema::create('fx_form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->enum('type', ['text', 'file', 'date', 'time', 'email', 'number']);
            $table->boolean('required')->default(false);
            $table->enum('form_type', ['buy', 'sell']);
            $table->json('currency_codes')->nullable(); // Array of currency codes this field applies to
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fx_form_fields');
    }
};
