<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clear table before altering (be careful with truncate in production)
        DB::table('cable_packages')->truncate();

        Schema::table('cable_packages', function (Blueprint $table) {
            // Step 1: Drop the foreign key constraint
            $table->dropForeign(['cable_id']); // or use the name: $table->dropForeign('cable_packages_cable_id_foreign');

            // Step 2: Drop the column
            $table->dropColumn('cable_id');

            // Step 3: Add new columns
            $table->string('provider_code')->after('name')->nullable();
            $table->string('availability')->after('provider_code')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cable_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('cable_id')->nullable();

            // Re-add the foreign key if needed
            $table->foreign('cable_id')->references('id')->on('cables')->onDelete('cascade');

            $table->dropColumn('provider_code');
            $table->dropColumn('availability');
        });
    }
};
