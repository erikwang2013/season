<?php

declare(strict_types=1);

use Erikwang2013\Season\SeasonService;

/** @var array $params */

$code = $params['erikwang2013/season']['default_country_code'] ?? null;

return [
    SeasonService::class => static fn (): SeasonService => new SeasonService(\is_string($code) ? $code : null),
];
