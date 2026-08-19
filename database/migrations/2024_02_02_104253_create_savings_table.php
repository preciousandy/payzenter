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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->unsignedBigInteger('balance');
            $table->string('type');
            $table->date('end_date')->nullable();
            $table->string('status');
            $table->boolean('if_auto_save')->default(false);
            $table->unsignedBigInteger('auto_save_amount')->nullable();
            $table->string('frequency')->nullable();
            $table->unsignedBigInteger('interest_accumulated')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
