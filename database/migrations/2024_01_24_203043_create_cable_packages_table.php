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
        Schema::create('cable_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cable_id')->references('id')->on('cables')->onDelete('cascade');
            $table->string('name');
            $table->string('package_code')->unique();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cable_packages');
    }
};
