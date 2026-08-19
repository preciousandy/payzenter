<?php

use App\Models\AjoContributionRound;
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
        Schema::table('ajo_contribution_rounds', function (Blueprint $table) {
            $table->string('status')->default(AjoContributionRound::STATUS_PENDING)->after('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajo_contribution_round', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
