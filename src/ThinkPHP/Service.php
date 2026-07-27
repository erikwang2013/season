<?php

declare(strict_types=1);

namespace Erikwang2013\Season\ThinkPHP;

use Erikwang2013\Season\SeasonService;
use think\Service as ThinkService;

class Service extends ThinkService
{
    public function register(): void
    {
        $this->app->bind(SeasonService::class, function ($app) {
            $config = $app->config->get('country_season', []);
            $code = $config['default_country_code'] ?? null;

            return new SeasonService(\is_string($code) ? $code : null);
        });
    }

    public function boot(): void
    {
    }
}
