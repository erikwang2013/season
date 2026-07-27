<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Laravel;

use Erikwang2013\Season\SeasonService;
use Illuminate\Support\ServiceProvider;

class CountrySeasonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/country_season.php', 'country_season'
        );

        $this->app->singleton(SeasonService::class, function ($app) {
            $code = $app['config']->get('country_season.default_country_code');

            return new SeasonService(\is_string($code) ? $code : null);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/country_season.php' => $this->app->configPath('country_season.php'),
            ], 'country-season-config');
        }
    }
}
