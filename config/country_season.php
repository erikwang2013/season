<?php

declare(strict_types=1);

/**
 * 默认国家代码（ISO 3166-1 alpha-2），用于 SeasonService::getSeasonForDefault()
 *
 * Laravel / ThinkPHP / Hyperf 发布或加载此配置后，可通过各框架 config 读取 country_season.default_country_code；
 * Yii2 通过 params 的 country_season 键读取，Yii3 由 config/params.php 包成 erikwang2013/season 命名空间。
 */
return [
    'default_country_code' => \function_exists('env')
        ? (env('COUNTRY_SEASON_DEFAULT') ?: 'CN')
        : (getenv('COUNTRY_SEASON_DEFAULT') ?: 'CN'),
];
