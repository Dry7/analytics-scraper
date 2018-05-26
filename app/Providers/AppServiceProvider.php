<?php

namespace App\Providers;

use App\Services\CountryService;
use App\Services\LoggerService;
use App\Services\ScraperService;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CountryService::class);
        $this->app->singleton(LoggerService::class, function () {
            return new LoggerService(config('analytics.logging.enabled'));
        });
        $this->app->singleton(ScraperService::class, function () {
            return new ScraperService(
                config('analytics.backend_host'),
                app(Client::class),
                app(LoggerService::class)
            );
        });
    }
}
