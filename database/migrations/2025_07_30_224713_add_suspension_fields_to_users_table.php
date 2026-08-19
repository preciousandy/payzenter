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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('suspension_duration_days')->nullable()->after('account_status');
            $table->datetime('suspended_until')->nullable()->after('suspension_duration_days');
            $table->text('suspension_reason')->nullable()->after('suspended_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['suspension_duration_days', 'suspended_until', 'suspension_reason']);
        });
    }
};
