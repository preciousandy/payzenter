<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class CurrencyConverterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $res = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Content-Type' => 'application/json',
        // ])->get("https://v6.exchangerate-api.com/v6/e2be252affbf397a16309cf0/latest/USD");

        // $data = $res->json();

        // $this->app->singleton('naira_to_dollar', function () use ($data) {
        //     return $data['conversion_rates']['NGN'];
        // });
        $this->app->singleton('naira_to_dollar', function () {
            return 1450;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
