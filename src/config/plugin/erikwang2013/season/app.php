<?php

declare(strict_types=1);

return [
    'enable' => true,
    'default_country_code' => \function_exists('env')
        ? (env('COUNTRY_SEASON_DEFAULT') ?: 'CN')
        : (getenv('COUNTRY_SEASON_DEFAULT') ?: 'CN'),
];
