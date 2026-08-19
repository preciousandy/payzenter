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
        Schema::create('international_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('recipient_name');
            $table->string('recipient_currency');
            $table->unsignedFloat('recipient_amount', 18);
            $table->unsignedFloat('amount_deducted_in_local_currency', 18);
            $table->string('recipient_email')->nullable();
            $table->string('recipient_account_number')->nullable();
            $table->string('recipient_bank')->nullable();
            $table->string('recipient_bank_swift')->nullable();
            $table->string('status');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('international_transfers');
    }
};
