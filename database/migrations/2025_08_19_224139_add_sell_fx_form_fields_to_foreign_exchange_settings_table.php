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
        Schema::table('foreign_exchange_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('foreign_exchange_settings', 'sell_fx_form_fields')) {
                $table->json('sell_fx_form_fields')->nullable()->after('form_fields');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foreign_exchange_settings', function (Blueprint $table) {
            if (Schema::hasColumn('foreign_exchange_settings', 'sell_fx_form_fields')) {
                $table->dropColumn('sell_fx_form_fields');
            }
        });
    }
};
