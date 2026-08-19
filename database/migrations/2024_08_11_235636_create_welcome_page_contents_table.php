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
        Schema::create('welcome_page_contents', function (Blueprint $table) {
            $table->id();
            $table->text('welcome_heading')->nullable();
            $table->text('welcome_sub_heading')->nullable();
            $table->text('features_section_heading')->nullable();
            $table->string('crypto_card_heading')->nullable();
            $table->text('crypto_card_text')->nullable();
            $table->string('holdings_card_heading')->nullable();
            $table->text('holdings_card_text')->nullable();
            $table->string('transfer_card_heading')->nullable();
            $table->text('transfer_card_text')->nullable();
            $table->string('savings_card_heading')->nullable();
            $table->text('savings_card_save_with_interest')->nullable();
            $table->text('savings_card_group_savings')->nullable();
            $table->string('ajo_savings_card_heading')->nullable();
            $table->text('ajo_savings_card_text')->nullable();
            $table->string('bills_card_heading')->nullable();
            $table->text('bills_card_text')->nullable();
            $table->string('gift_cards_card_heading')->nullable();
            $table->text('gift_cards_card_text')->nullable();
            $table->string('about_us_section_heading')->nullable();
            $table->text('about_us_section_sub_heading')->nullable();
            $table->text('mission_statement')->nullable();
            $table->text('vision_statement')->nullable();
            $table->string('testimonial_section_heading')->nullable();
            $table->string('contact_us_section_heading')->nullable();
            $table->text('contact_us_section_sub_heading')->nullable();
            $table->text('faq_section_sub_heading')->nullable();
            $table->string('call_to_action_section_heading')->nullable();
            $table->text('call_to_action_section_sub_heading')->nullable();
            $table->text('footer_section_sub_heading')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welcome_page_contents');
    }
};
