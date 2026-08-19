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
        if (Schema::hasTable('user_virtual_accounts')) {
            return;
        }

        Schema::create('user_virtual_accounts', function (Blueprint $table) {
            $table->id();
            
            
            $table->foreignId('user_id')->constrained()->unique(); 
            
           
            $table->string('account_number')->unique();
            $table->string('external_id')->nullable();
            $table->string('account_name');
            $table->string('bank_code', 10);
            $table->string('bank_name');
            
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('user_virtual_accounts');
    }
};
