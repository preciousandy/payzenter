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
        Schema::table('international_transfers', function (Blueprint $table) {
            $table->dropColumn('message');
            $table->dropColumn('recipient_bank_swift');
            $table->dropColumn('recipient_email');
            $table->string("reference")->nullable()->unique();
            $table->string("bank_code")->nullable();
            $table->string("bank_routing_number")->nullable();
            $table->string("bank_swift_code")->nullable();
            $table->text("description")->nullable();
            $table->float("amount_deducted_in_local_currency", 12, 2)->change();
            $table->float("recipient_amount", 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('international_transfers', function (Blueprint $table) {
            $table->string('message')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_bank_swift')->nullable();
            $table->dropColumn("bank_code");
            $table->dropColumn("bank_routing_number");
            $table->dropColumn("bank_swift_code");
            $table->dropColumn("description");
            $table->dropColumn("reference");
        });
    }
};
