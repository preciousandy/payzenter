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
            $table->boolean('is_bvn_verified')->nullable()->default(false)->after('email_verified_at');
            $table->string('bvn')->nullable()->after('is_bvn_verified');
            $table->string('bvn_reference')->nullable()->after('bvn');
            $table->string('nin')->nullable()->after('bvn_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_bvn_verified');
            $table->dropColumn('bvn');
            $table->dropColumn('bvn_reference');
            $table->dropColumn('nin');
        });
    }
};
