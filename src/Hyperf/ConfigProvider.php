<?php

declare(strict_types=1);

namespace Erikwang2013\Season\Hyperf;

use Erikwang2013\Season\SeasonService;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                SeasonService::class => function () {
                    $code = null;

                    if (\function_exists('config')) {
                        $code = config('country_season.default_country_code');
                    }

                    return new SeasonService(\is_string($code) ? $code : null);
                },
            ],
            'publish' => [
                [
                    'id' => 'country-season-config',
                    'description' => 'Country season default config',
                    'source' => __DIR__ . '/../../config/country_season.php',
                    'destination' => (\defined('BASE_PATH') ? BASE_PATH . '/' : '') . 'config/autoload/country_season.php',
                ],
            ],
        ];
    }
}
