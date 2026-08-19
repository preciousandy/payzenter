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
        Schema::table('welcome_page_contents', function (Blueprint $table) {
            $table->dropColumn([
                'transfer_card_heading',
                'transfer_card_text',
                'ajo_savings_card_heading',
                'ajo_savings_card_text',
                'savings_card_heading',
                'savings_card_save_with_interest',
                'savings_card_group_savings',
                'footer_section_sub_heading',
            ]);
            $table->text('features_section_sub_heading')->nullable();
            $table->text('international_transfer_card_heading')->nullable();
            $table->text('international_transfer_card_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('welcome_page_contents', function (Blueprint $table) {
            $table->string('transfer_card_heading')->nullable();
            $table->text('transfer_card_text')->nullable();
            $table->string('savings_card_heading')->nullable();
            $table->string('ajo_savings_card_heading')->nullable();
            $table->text('ajo_savings_card_text')->nullable();
            $table->string('savings_card_save_with_interest')->nullable();
            $table->string('savings_card_group_savings')->nullable();
            $table->string('footer_section_sub_heading')->nullable();
            $table->dropColumn([
                'features_section_sub_heading',
                'international_transfer_card_heading',
                'international_transfer_card_text',
            ]);
        });
    }
};
