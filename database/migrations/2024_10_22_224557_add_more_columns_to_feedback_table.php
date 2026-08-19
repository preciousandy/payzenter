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
        if (!Schema::hasTable('feedback')) {
            return;
        }

        if (!Schema::hasColumn('feedback', 'phone')) {
            Schema::table('feedback', function (Blueprint $table) {
                if (Schema::hasColumn('feedback', 'subject')) {
                    $table->string('phone')->nullable()->after('subject');
                } else {
                    $table->string('phone')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('feedback')) {
            return;
        }

        if (Schema::hasColumn('feedback', 'phone')) {
            Schema::table('feedback', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
};
