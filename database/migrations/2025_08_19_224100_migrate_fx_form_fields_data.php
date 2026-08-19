<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ForeignExchangeSetting;
use App\Models\FxFormField;
use App\Models\ExchangeRate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing data from ForeignExchangeSetting to FxFormField
        $settings = ForeignExchangeSetting::first();

        if ($settings) {
            $allCurrencies = ExchangeRate::distinct()->pluck('code')->toArray();

            // Migrate buy FX fields
            if ($settings->form_fields) {
                foreach ($settings->form_fields as $field) {
                    FxFormField::create([
                        'label' => $field['label'],
                        'type' => $field['type'],
                        'required' => $field['required'] ?? false,
                        'form_type' => 'buy',
                        'currency_codes' => $allCurrencies // Apply to all currencies initially
                    ]);
                }
            }

            // Migrate sell FX fields
            if ($settings->sell_fx_form_fields) {
                foreach ($settings->sell_fx_form_fields as $field) {
                    FxFormField::create([
                        'label' => $field['label'],
                        'type' => $field['type'],
                        'required' => $field['required'] ?? false,
                        'form_type' => 'sell',
                        'currency_codes' => $allCurrencies // Apply to all currencies initially
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove migrated data
        FxFormField::truncate();
    }
};
