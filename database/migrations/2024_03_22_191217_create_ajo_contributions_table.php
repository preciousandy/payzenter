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
        Schema::create('ajo_contributions', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("type");
            $table->string("frequency");
            $table->unsignedInteger("no_of_members");
            $table->unsignedBigInteger("amount");
            $table->date("start_date");
            $table->integer("current_round")->default(0);
            $table->string("cover_image")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajo_contributions');
    }
};
