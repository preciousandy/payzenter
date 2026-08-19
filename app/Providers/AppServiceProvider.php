<?php

namespace App\Providers;

use App\Services\HashIdService;
use App\Services\TransferService;
use App\Services\UserService;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserService::class, function () {
            return new UserService();
        });

        $this->app->singleton(HashIdService::class, function () {
            return new HashIdService();
        });


        $this->app->singleton(TransferService::class, function () {
            return new TransferService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }

        Request::macro('hasValidSignature', function ($absolute = true) {
            if (config('app.env') === 'local') return true;
            return URL::hasValidSignature($this, $absolute);
        });

        Request::macro('hasValidRelativeSignature', function () {
            if (config('app.env') === 'local') return true;
            return URL::hasValidSignature($this, $absolute = false);
        });

        Request::macro('hasValidSignatureWhileIgnoring', function ($ignoreQuery = [], $absolute = true) {
            if (config('app.env') === 'local') return true;
            return URL::hasValidSignature($this, $absolute, $ignoreQuery);
        });
    }
}
