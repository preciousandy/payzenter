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
        Schema::create('group_savings_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_savings_id')->constrained('group_savings');
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedFloat('amount', 18);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_savings_donations');
    }
};
