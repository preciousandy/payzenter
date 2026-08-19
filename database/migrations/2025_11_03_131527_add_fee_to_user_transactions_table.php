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
        if (!Schema::hasTable('user_transactions')) {
            return;
        }

        if (!Schema::hasColumn('user_transactions', 'fee')) {
            Schema::table('user_transactions', function (Blueprint $table) {
                $table->decimal('fee', 10, 2)->default(0.00)->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('user_transactions')) {
            return;
        }

        if (Schema::hasColumn('user_transactions', 'fee')) {
            Schema::table('user_transactions', function (Blueprint $table) {
                $table->dropColumn('fee');
            });
        }
    }
};
