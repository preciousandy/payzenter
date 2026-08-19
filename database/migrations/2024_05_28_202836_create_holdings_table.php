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
        Schema::create('holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('asset_name');
            $table->string('asset_symbol');
            $table->string('asset_type');
            $table->unsignedFloat('asset_price', 18);
            $table->unsignedFloat('amount', 18);
            $table->unsignedFloat('dollar_rate', 18)->nullable();
            $table->date('end_date')->nullable();
            $table->string('status');
            $table->string('icon_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holdings');
    }
};
