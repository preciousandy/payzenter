<?php

use App\Models\Kyc;
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
            $table->string('kyc_status')->default(Kyc::STATUS_UNVERIFIED)->after('bvn_reference');
        });
        Schema::table('kycs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kyc_status');
        });

        Schema::table('kycs', function (Blueprint $table) {
            $table->string('status')->default(Kyc::STATUS_UNVERIFIED);
        });
    }
};
