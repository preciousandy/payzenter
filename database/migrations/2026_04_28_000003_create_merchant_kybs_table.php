<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_kybs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('surname')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->text('address')->nullable();
            $table->boolean('pep_declaration')->default(false);
            $table->string('social_media_handle')->nullable();
            $table->string('identity_document_path')->nullable();
            $table->string('proof_of_address_path')->nullable();

            $table->string('business_name')->nullable();
            $table->text('business_description')->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_website')->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_type')->nullable();
            $table->string('bvn')->nullable();
            $table->string('tin')->nullable();
            $table->text('business_social_media_handles')->nullable();
            $table->string('business_logo_path')->nullable();

            $table->string('settlement_bank_id')->nullable();
            $table->string('settlement_bank_name')->nullable();
            $table->string('settlement_account_number')->nullable();
            $table->string('settlement_account_name')->nullable();
            $table->timestamp('settlement_verified_at')->nullable();

            $table->string('cac_registration_number')->nullable();
            $table->string('business_resolution_path')->nullable();
            $table->string('company_profile_path')->nullable();
            $table->string('status_report_path')->nullable();
            $table->string('business_address_verification_path')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_kybs');
    }
};

