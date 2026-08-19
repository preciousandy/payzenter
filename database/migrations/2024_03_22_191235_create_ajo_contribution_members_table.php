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
        Schema::create('ajo_contribution_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajo_contribution_id')->constrained('ajo_contributions')->onDelete('cascade');
            $table->foreignId('user_id');
            $table->boolean("is_contributing")->default(true);
            $table->boolean("if_auto_save")->default(false);
            $table->string("role");
            $table->unsignedInteger("order_number")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajo_contribution_members');
    }
};
