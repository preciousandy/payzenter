<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('website_link')->nullable();
            $table->string('business_type')->nullable();

        
            $table->string('nin_image_path')->nullable();
            $table->string('cac_image_path')->nullable();
            $table->string('utility_bill_path')->nullable();
        });
    }

    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'website_link', 'business_type', 'nin_image_path', 'cac_image_path', 'utility_bill_path']);
        });
    }
};