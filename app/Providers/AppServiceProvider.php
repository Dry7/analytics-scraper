<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CountryService;
use App\Services\Html\Parsers\VKDate;
use App\Services\Html\Parsers\VKPost;
use App\Services\Html\Vkontakte\VKLeftAdsCrawler;
use App\Services\LoggerService;
use App\Services\ScraperService;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use Laravel\Lumen\Application;

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
        $this->app->singleton(VKPost::class);
        $this->app->singleton(VKDate::class);
        $this->app->singleton(VKLeftAdsCrawler::class, static function (Application $app) {
            return new VKLeftAdsCrawler($app[Client::class]);
        });
    }
}
