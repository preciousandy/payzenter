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
        Schema::create('group_savings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->text('description');
            $table->unsignedBigInteger('balance');
            $table->unsignedBigInteger('target_amount')->nullable();
            $table->date('end_date');
            $table->unsignedBigInteger('member_target_amount')->nullable();
            $table->timestamps();
        });

        Schema::create('group_savings_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('group_savings_id')->references('id')->on('group_savings');
            $table->string('status');
            $table->string('role');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_savings');
        Schema::dropIfExists('group_savings_members');
    }
};
