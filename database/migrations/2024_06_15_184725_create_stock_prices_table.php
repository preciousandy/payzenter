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
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('symbol')->nullable();
            // $table->text('about')->nullable();
            $table->float('change', 15)->nullable();
            $table->float('change_percent', 15)->nullable();
            $table->float('price', 15)->nullable();
            // $table->float('company_market_cap', 20)->nullable();
            // $table->float('open', 15)->nullable();
            // $table->float('high', 15)->nullable();
            // $table->float('low', 15)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
