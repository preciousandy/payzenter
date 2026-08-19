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
        Schema::create('ajo_contribution_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajo_contribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable();
            $table->integer('order');
            $table->unsignedBigInteger('balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajo_contribution_rounds');
    }
};
